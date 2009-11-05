#include <QSqlQuery>
#include <QSqlError>
#include <QXmlStreamReader>
#include <QBuffer>
#include <QTime>

#include "PhpProcess.h"
#include "Generator.h"
#include "ComponentData.h"

#define ifDebugGeneratorBuild(x)
#define ifDebugGeneratorBuildInherit(x)
#include "Unserializer.h"

QList<Generator*> Generator::generators;
QHash<Generator::Type, int> Generator::buildCallCount;

QList<Generator*> Generator::inheritGenerators()
{
    static QList<Generator*> cache;
    if (cache.isEmpty()) {
        foreach (Generator *g, generators) {
            if (g->componentTypes & Generator::TypeInherit) {
                cache << g;
            }
        }
    }
    return cache;
}

/*
bool canHaveComponentClassAsChild(Generator *generator, ComponentClass cc)
{
    QPair<Generator*, ComponentClass> i;
    i.first = generator;
    i.second = cc;

    static QHash<QPair<Generator*, ComponentClass>, bool> cache;
    if (cache.contains(i)) return cache[i];

    cache[i] = false;

    if (generator->childComponentClasses().contains(cc)) {
        cache[i] = true;
        return true;
    }

    foreach (ComponentClass c, generator->childComponentClasses()) {
        foreach (Generator *g, Generator::generators) {
            if (g->componentClass == c) {
                if (canHaveComponentClassAsChild(g, cc)) {
                    cache[i] = true;
                    return true;
                }
            }
        }
    }
    return false;
}

bool BuildOnlyComponentClassStrategy::skip(ComponentData *parent) const
{
    bool skip = true;
    foreach (Generator *g, Generator::generators) {
        if (parent->componentClass() == g->componentClass) {
            if (canHaveComponentClassAsChild(g, cc)) {
                skip = false;
            }
        }
    }
    foreach (Generator *g, Generator::inheritGenerators()) {
        if (canHaveComponentClassAsChild(g, cc)) {
            skip = false;
        }
    }
    return skip;
}
*/
bool canHavePagesGeneratorAsChild(Generator *generator)
{
    static QHash<Generator *, bool> cache;
    if (cache.contains(generator)) return cache[generator];

    cache[generator] = false;

    if (generator->componentTypes & Generator::TypePagesGenerator) {
        cache[generator] = true;
        return true;
    }

    foreach (ComponentClass c, generator->childComponentClasses()) {
        foreach (Generator *g, Generator::generators) {
            if (g->componentClass == c) {
                if (canHavePagesGeneratorAsChild(g)) {
                    cache[generator] = true;
                    return true;
                }
            }
        }
    }
    return false;
}

bool BuildOnlyPagesGeneratorStrategy::skip(ComponentData *parent) const
{
    foreach (Generator *g, Generator::generators) {
        if (parent->componentClass() == g->componentClass) {
            if (canHavePagesGeneratorAsChild(g)) {
                return false;
            }
        }
    }
    return true;
}


enum DbIdShortcutType {
    NoDbIdShortcut,
    DirectDbIdShortcut,
    IndirectDbIdShortcut
};
DbIdShortcutType canHaveDbIdShortcutAsChild(Generator *generator)
{
    static QHash<Generator *, DbIdShortcutType> cache;
    if (cache.contains(generator)) return cache[generator];

    cache[generator] = NoDbIdShortcut;

    if (!generator->dbIdPrefix.isEmpty()) {
        cache[generator] = DirectDbIdShortcut;
        return DirectDbIdShortcut;
    }

    foreach (ComponentClass c, generator->childComponentClasses()) {
        foreach (Generator *g, Generator::generators) {
            if (g->componentClass == c) {
                if (canHaveDbIdShortcutAsChild(g) != NoDbIdShortcut) {
                    cache[generator] = IndirectDbIdShortcut;
                    return IndirectDbIdShortcut;
                }
            }
        }
    }
    return NoDbIdShortcut;
}
bool BuildWithDbIdShortcutStrategy::skip(ComponentData* parent) const
{
    foreach (Generator *g, Generator::generators) {
        if (parent->componentClass() == g->componentClass) {
            if (canHaveDbIdShortcutAsChild(g) == IndirectDbIdShortcut) {
                return false;
            }
        }
    }
    return true;
}


void Generator::buildWithGenerators(ComponentData* parent, const BuildStrategy *buildStrategy)
{
    if (buildStrategy && buildStrategy->skip(parent)) return;

    parent->childrenLock()->lockForRead();

    bool childrenBuilt = parent->m_childrenBuilt;

    if (!childrenBuilt) {
        parent->childrenLock()->unlock();
        parent->childrenLock()->lockForWrite();

        parent->m_childrenBuilt = true;

        foreach (Generator *g, generators) {
            if (g->componentClass == parent->componentClass()) {
                g->build(parent);
            }
        }

        //build inherited children
        if (parent->generator() && parent->generator()->componentTypes & Generator::TypeInherits) {
            ComponentData *p = parent;
            while ((p = p->parent())) {
                foreach (Generator *g, inheritGenerators()) {
                    if (!(g->componentTypes & Generator::TypeUnique) && g->componentClass == p->componentClass()) {
                        g->build(parent);
                    }
                }
            }
        }
    }

    //build recursive
    if (buildStrategy->recurse()) {
        foreach (ComponentData *c, parent->m_children) {
            buildWithGenerators(c, buildStrategy);
        }
    }

    if (!childrenBuilt && parent->generator() && parent->generator()->componentTypes & Generator::TypeInherits) {
        ifDebugGeneratorBuildInherit( qDebug() << "----->inherits" << parent->generator()->componentClass << parent->generator()->key << parent->componentId(); )
        ComponentData *p = parent;
        while ((p = p->parent())) {
            ifDebugGeneratorBuildInherit( qDebug() << "check if inherits us something" << p->componentId(); )
            foreach (Generator *g, inheritGenerators()) {
                if (g->componentTypes & Generator::TypeUnique && g->componentClass == p->componentClass()) {
                    ifDebugGeneratorBuildInherit( qDebug() << "it's unique"; )
                    //TODO das sollte glaub ich besser in inheritedUniqueChildren oder so
                    //damit keine endlosschleifen bei rekursiven aktionen rauskommen
                    foreach (ComponentData *c, p->m_children) {
                        if (c->generator() == g) {
                            ifDebugGeneratorBuildInherit( qDebug() << "adding" << c->componentId() << "to" << parent->componentId(); )
                            parent->addChildren(c);
                        } else {
                            ifDebugGeneratorBuildInherit( qDebug() << "NOT adding" << c->componentId() << "to" << parent->componentId(); )
                        }
                    }
                }
            }
        }
    }

    if (!childrenBuilt) {
        QHash<ComponentData *, IndexedString> boxHash;
        for (int i=0; i < parent->m_children.count(); ++i) {
            ComponentData *c = parent->m_children.at(i);
            if (!c->box().isEmpty()) {
                boxHash[c] = c->box();
            }
        }
        for (int i=0; i < parent->m_children.count(); ++i) {
            ComponentData *c = parent->m_children.at(i);
            if (boxHash.contains(c)) {
                for (int i2=0; i2 < parent->m_children.count(); ++i2) {
                    ComponentData *c2 = parent->m_children.at(i2);
                    if (boxHash.contains(c2) && c!=c2 && boxHash[c2] == boxHash[c]) {
                        //if (c->priority() == c2->priority()) {
                            //qDebug() << "same priority" << c->componentId() << c->treeLevel() << c2->componentId() << c2->treeLevel();
                        //}
                        if (c->priority() > c2->priority()
                            || (c->priority() == c2->priority() && c->treeLevel() <= c2->treeLevel())) {
                            parent->m_children.removeAt(i2);
                            i2--;
                            if (i2 > i) i--;
                            //qDebug() << "delte" << c2->componentId() << "not" << c->componentId();
                            if (!(c2->generator()->componentTypes & Generator::TypeInherit)) {
                                //TODO: leaked, manchmal muss auch in inherit gelˆscht werden (kann vielleicht woanders gelˆst werden)
                                delete c2;
                            }
                        } else {
                            parent->m_children.removeAt(i);
                            i--;
                            if (i2 > i) i2--;
                            //qDebug() << "delte" << c->componentId() << "not" << c2->componentId();
                            if (!(c->generator()->componentTypes & Generator::TypeInherit)) {
                                //TODO: leaked, manchmal muss auch in inherit gelˆscht werden (kann vielleicht woanders gelˆst werden)
                                delete c;
                            }
                        }
                    }
                }
            }
        }
    }

    parent->childrenLock()->unlock();
}

void Generator::handleChangedRow(Generator::ChangedRowMethod method, IndexedString model, const QString& id)
{
    switch (method) {
        case RowUpdated:
            foreach (Generator *g, generators) {
                if (g->model == model) {
                    g->preload(); //tut im moment noch _alles_ preloaden, mˆglicherweise nur eine id preloaden wenn zu langsam
                    foreach (ComponentData *d, g->builtComponents) { //das benˆtigt womˆglich einen index wenns zu langsam ist
                        if (d->childId() == id) {
                            foreach (const IndexedString &field, d->rowData.keys()) {
                                Q_ASSERT(dynamic_cast<GeneratorWithModel*>(g));
                                static_cast<GeneratorWithModel*>(g)->fetchRowData(d->parent(), field, id);
                            }
                            g->refresh(d);
                            break;
                        }
                    }
                }
            }
            break;
        case RowInserted:
            foreach (Generator *g, generators) {
                if (g->model == model) {
                    g->preload(); //tut im moment noch _alles_ preloaden, mˆglicherweise nur eine id preloaden wenn zu langsam
                    foreach (ComponentData *d, ComponentData::getComponentsByClass(g->componentClass)) {
                        QWriteLocker locker(&d->m_childrenLock);
                        if (d->m_childrenBuilt) {
                            g->buildSingle(d, id);
                            locker.unlock();
                            foreach (ComponentData *sibling, d->children()) {
                                if (sibling->generator() == g && sibling->childId() != id) {
                                    foreach (const IndexedString &field, sibling->rowData.keys()) {
                                        Q_ASSERT(dynamic_cast<GeneratorWithModel*>(g));
                                        static_cast<GeneratorWithModel*>(g)->fetchRowData(d, field, id);
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            break;
        case RowDeleted:
            foreach (Generator *g, generators) {
                if (g->model == model) {
                    foreach (ComponentData *d, g->builtComponents) { //das benˆtigt womˆglich einen index wenns zu langsam ist
                        if (d->childId() == id) {
                            Q_ASSERT(!(d->generator()->componentTypes & Generator::TypeInherit));
                            if (!(d->generator()->componentTypes & Generator::TypeInherit)) {
                                d->parent()->m_childrenLock.lockForWrite();
                                delete d;
                                d->parent()->m_childrenLock.unlock();
                            }
                        }
                    }
                }
            }
            break;
    }
}

void GeneratorWithModel::fetchRowData(ComponentData* parent, IndexedString field, const QString& onlyId)
{
    static QMutex lock;
    QMutexLocker locker(&lock);
    if (parent->m_fetchedRowData.contains(field)) return;
    parent->m_fetchedRowData.insert(field);
    QList<QByteArray> args;
    args << QByteArray("--field=") + field.toString().toUtf8();
    args << QByteArray("--model=") + model.toString().toUtf8();
    if (!onlyId.isEmpty()) {
        args << QByteArray("--id=") + onlyId.toUtf8();
    }

    QTime stopWatch;
    stopWatch.start();
    qDebug() << "model-get-rows" << args;
    QByteArray data = PhpProcess::getInstance()->call("model-get-rows", args);
    qDebug() << stopWatch.elapsed() << "ms";

    QBuffer buffer(&data);
    buffer.open(QIODevice::ReadOnly);
    Unserializer u(&buffer);

    QHash<int, ComponentData*> childIds = parent->childIdsHash();
    int rowCount = 0;
    int fetchedButNotUsed = 0;
    if (u.device()->peek(2) == "N;") {
        //feld gibts ned
    } else if (u.device()->peek(2) == "s:") {
        //wir haben einen sql string bekommen
        QString sql = QString::fromUtf8(u.readString());
        qDebug() << sql;
        QSqlQuery query;
        if (!query.exec(sql)) {
            qCritical() << "can't execute query GeneratorWithModel::fetchRowData" << query.lastError() << sql;
            Q_ASSERT(0);
            return;
        }
        while (query.next()) {
            int id = query.value(0).toInt();
            QVariant value = query.value(1);
            if (childIds.contains(id)) childIds[id]->rowData[field] = value;
            if (childIds.contains(-id)) childIds[-id]->rowData[field] = value;
            if (!childIds.contains(-id) && !childIds.contains(id)) {
                fetchedButNotUsed++;
            }
            rowCount++;
        }
    } else {
        //wir haben gleich die daten bekommen
        rowCount = u.readArrayStart();
        for (int i=0; i < rowCount; ++i) {
            int id = u.readInt();
            QVariant value =  u.readVariant();
            if (childIds.contains(id)) {
                childIds[id]->rowData[field] = value;
            } else if (childIds.contains(-id)) {
                childIds[-id]->rowData[field] = value;
            } else {
                fetchedButNotUsed++;
            }
        }
        u.readArrayEnd();
    }
    qDebug() << rowCount << "rows fetched";
    if (fetchedButNotUsed) {
        qWarning() << fetchedButNotUsed << "rows fetched but not used";
    }
}


QList<int> GeneratorWithModel::fetchIds(ComponentData* parent, const Select& select) const
{
    Q_UNUSED(parent); //TODO: k√∂nnte verwendet werden um nicht zuviel zu fetchen

    QList<QByteArray> args;
    args << QByteArray("--model=") + model.toString().toUtf8();
    args << QByteArray("--select=") + serialize(select).replace('\0', "\\0");;

    QTime stopWatch;
    stopWatch.start();
    QByteArray data = PhpProcess::getInstance()->call("model-get-ids", args);
    qDebug() << "model-get-ids" << args << stopWatch.elapsed() << "ms";

    QBuffer buffer(&data);
    buffer.open(QIODevice::ReadOnly);
    Unserializer u(&buffer);

    QString sql = QString::fromUtf8(u.readString());
    qDebug() << sql;
    QSqlQuery query;
    if (!query.exec(sql)) {
        qCritical() << "can't execute query GeneratorWithModel::fetchRowData" << query.lastError() << sql;
        Q_ASSERT(0);
        return QList<int>();
    }
    QList<int> ret;
    while (query.next()) {
        ret << query.value(0).toInt();
    }
    return ret;
}


bool GeneratorWithModel::isVisible(const ComponentData* d) const
{
    const_cast<GeneratorWithModel*>(this)
        ->fetchRowData(const_cast<ComponentData*>(d->parent()), IndexedString("visible"));
    if (!d->rowData.contains(IndexedString("visible"))) {
        return true;
    }
    if (d->rowData[IndexedString("visible")].toInt()) {
        return true;
    }
    return false;
}


bool Generator::showInMenu(ComponentData* d)
{
    Q_ASSERT(d->generator() == this);
    return (d->componentTypes() & Generator::TypeShowInMenu);
}

bool Generator::isVisible(const ComponentData* d) const
{
    Q_UNUSED(d);
    return true;
}

void GeneratorStatic::build(ComponentData* parent)
{
    buildCallCount[Static]++;
    ifDebugGeneratorBuild( qDebug() << "GeneratorStatic::build" << componentClass << key << parent->componentId();)
    QHashIterator<IndexedString, ComponentClass> i(component);
    while (i.hasNext()) {
        i.next();
        ComponentData *d = new ComponentData(this, parent, idSeparator, i.key(), i.value());
        if (componentTypes & TypePseudoPage) {
            QString fn = filename;
            if (fn.isEmpty()) {
                fn = name;
            }
            if (fn.isEmpty()) {
                fn = i.key().toString();
            }
            d->setFilename(fn);
        }
        if (componentTypes & TypePage) {
            QString n = name;
            if (n.isEmpty()) {
                n = i.key().toString();
            }
            d->setName(n);
        }
        parent->addChildren(d);
    }
}

void GeneratorStatic::buildSingle(ComponentData* parent, const QString& id)
{
    Q_UNUSED(parent);
    Q_UNUSED(id);
    Q_ASSERT(0); //doesn't make any sense
}

void GeneratorStatic::refresh(ComponentData* d)
{
    Q_UNUSED(d);
    Q_ASSERT(0); //doesn't make any sense
}

QList<ComponentClass> GeneratorStatic::childComponentClasses()
{
    return component.values();
}

QList<IndexedString> GeneratorStatic::childComponentKeys()
{
    return component.keys();
}

void GeneratorTable::build(ComponentData* parent)
{
    buildCallCount[Table]++;
    ifDebugGeneratorBuild( qDebug() << "GeneratorTable::build" << parent->componentId(); )
    foreach (Row row, rows) {
        ComponentData *d = new ComponentData(this, parent, idSeparator, row.id, component);
        d->setDbIdPrefix(dbIdPrefix);
        //d->name = row.name;
        parent->addChildren(d);
    }
}

void GeneratorTable::buildSingle(ComponentData* parent, const QString& id)
{
    Q_UNUSED(parent);
    Q_UNUSED(id);
    Q_ASSERT(0); //not yet implemented
}

void GeneratorTable::refresh(ComponentData* d)
{
    Q_UNUSED(d);
    //nothing to do as long as no name is set in build()
}

QList<ComponentClass> GeneratorTable::childComponentClasses()
{
    QList<ComponentClass> ret;
    ret << component;
    return ret;
}

QList<IndexedString> GeneratorTable::childComponentKeys()
{
    QList<IndexedString> ret;
    ret << key;
    return ret;
}


void GeneratorTableSql::_build(ComponentData* parent, QSqlQuery &query)
{
    while (query.next()) {
        int id = query.value(0).toInt();
        ComponentData *d = new ComponentData(this, parent, idSeparator, id, component);
        d->setDbIdPrefix(dbIdPrefix);
        parent->addChildren(d);
    }
}


void GeneratorTableSql::build(ComponentData* parent)
{
    buildCallCount[TableSql]++;
    ifDebugGeneratorBuild( qDebug() << "GeneratorTableSql::build" << parent->dbId(); )
    QString sql = "SELECT id FROM "+tableName;
    if (whereComponentId) {
        sql += " WHERE component_id='"+parent->dbId()+"'";
    }
    QSqlQuery query;
    if (!query.exec(sql)) {
        qCritical() << "can't execute query GeneratorTableSql::build";
        Q_ASSERT(0);
        return;
    }
    _build(parent, query);
}

void GeneratorTableSql::buildSingle(ComponentData* parent, const QString& id)
{
    buildCallCount[TableSql]++;
    QString sql = "SELECT id FROM "+tableName;
    if (whereComponentId) {
        sql += " WHERE component_id='"+parent->dbId()+"' AND ";
    } else {
        sql += " WHERE ";
    }
    sql += "id=:id";
    QSqlQuery query;
    query.bindValue(":id", id);
    if (!query.exec(sql)) {
        qCritical() << "can't execute query GeneratorTableSql::build";
        Q_ASSERT(0);
        return;
    }
    _build(parent, query);
}

void GeneratorTableSql::refresh(ComponentData* d)
{
    Q_UNUSED(d);
    //nothing to do as long as no name/filename is set in _build()
}

QList<ComponentClass> GeneratorTableSql::childComponentClasses()
{
    QList<ComponentClass> ret;
    ret << component;
    return ret;

}

QList<IndexedString> GeneratorTableSql::childComponentKeys()
{
    QList<IndexedString> ret;
    ret << key;
    return ret;
}

void GeneratorTableSqlWithComponent::preload()
{
    QSqlQuery query;
    QString sql = "SELECT id, component ";
    if (whereComponentId) {
        sql += ", component_id ";
    }
    sql += "FROM "+tableName;
    if (!query.exec(sql)) {
        qCritical() << "can't execute query GeneratorTableSqlWithComponent::preload" << query.lastError() << sql;
        Q_ASSERT(0);
        return;
    }
    data.clear();
    while (query.next()) {
        QList<QPair<int, IndexedString> > l;
        QString componentId;
        if (whereComponentId) {
            componentId = query.value(2).toString();
        }
        data[componentId] << qMakePair(query.value(0).toInt(), component[IndexedString(query.value(1).toString())]);
    }
}

QList< QPair< int, ComponentClass > > GeneratorTableSqlWithComponent::_items(ComponentData* parent)
{
    QList<QPair<int, ComponentClass> > items;
    if (whereComponentId) {
        return data[parent->dbId()];
    }
    return data[QString()];
}

void GeneratorTableSqlWithComponent::build(ComponentData* parent)
{
    buildCallCount[TableSqlWithComponent]++;
    QList< QPair< int, ComponentClass > > items = _items(parent);
    QPair<int, ComponentClass> i;
    foreach (i, items) {
        ComponentData *d = new ComponentData(this, parent, idSeparator, i.first, i.second);
        d->setDbIdPrefix(dbIdPrefix);
        parent->addChildren(d);
    }
}

void GeneratorTableSqlWithComponent::buildSingle(ComponentData* parent, const QString& id)
{
    buildCallCount[TableSqlWithComponent]++;

    QList< QPair< int, ComponentClass > > items = _items(parent);
    QPair<int, ComponentClass> i;
    foreach (i, items) {
        if (i.first == id.toInt()) {
            ComponentData *d = new ComponentData(this, parent, idSeparator, i.first, i.second);
            d->setDbIdPrefix(dbIdPrefix);
            parent->addChildren(d);
        }
    }
}

void GeneratorTableSqlWithComponent::refresh(ComponentData* d)
{
    ComponentData* parent = d->parent();
    QList< QPair< int, ComponentClass > > items = _items(parent);
    QPair<int, ComponentClass> i;
    foreach (i, items) {
        if (i.first == d->childId().toInt()) {
            if (d->componentClass() != i.second) {
                parent->childrenLock()->lockForWrite();
                delete d;
                buildSingle(parent, QString::number(i.first));
                parent->childrenLock()->unlock();
            }
            break;
        }
    }
}

QList<QString> GeneratorTableSqlWithComponent::fetchParentDbIds(ComponentClass cc)
{
    Q_ASSERT(whereComponentId);
    QSqlQuery query;
    QString sql = "SELECT component_id";
    sql += " FROM "+tableName;
    sql += " WHERE component IN (";
    foreach (IndexedString c, component.keys(cc)) {
        //TODO: quoting
        sql += QString("'%1', ").arg(c.toString());
    }
    sql = sql.left(sql.length()-2);
    sql += ")";
    if (!query.exec(sql)) {
        qCritical() << "can't execute query GeneratorTableSqlWithComponent::fetchParentDbIds" << query.lastError() << sql;
        Q_ASSERT(0);
        return QList<QString>();
    }
    QList<QString> ret;
    while (query.next()) {
        ret << query.value(0).toString();
    }
    return ret;
}


QList<ComponentClass> GeneratorTableSqlWithComponent::childComponentClasses()
{
    return component.values();
}

QList<IndexedString> GeneratorTableSqlWithComponent::childComponentKeys()
{
    return component.keys();
}

QByteArray GeneratorLoadSql::_sql(ComponentData* parent)
{
    QList<QByteArray> arg;
    arg << QByteArray("--componentId=") + parent->componentId().toUtf8();
    arg << QByteArray("--generator=") + key.toString().toUtf8();
    return PhpProcess::getInstance()->call("generator-sql", arg);
}

void GeneratorLoadSql::_build(ComponentData* parent, QSqlQuery query)
{
    while (query.next()) {
        int id = query.value(0).toInt();
        ComponentData *d = new ComponentData(this, parent, idSeparator, id, component);
        d->setDbIdPrefix(dbIdPrefix);
        d->setName(query.value(1).toString());
        //TODO: maxNameLength, uniqueFilename, maxFilenameLength
        //d->setFilename(query.value(2).toString());
        if (d->filename().isEmpty()) {
            d->setFilename(d->name());
        }
        parent->addChildren(d);
    }
}

void GeneratorLoadSql::build(ComponentData* parent)
{
    buildCallCount[LoadSql]++;
    ifDebugGeneratorBuild( qDebug() << "GeneratorLoadSql::build" << parent->componentId() << sql; )
    QByteArray sql = _sql(parent);
    if (sql.isEmpty()) return;
    QSqlQuery query;
    if (!query.exec(sql)) {
        qCritical() << "can't execute query GeneratorLoadSql::build" << query.lastError() << query.executedQuery();
        Q_ASSERT(0);
        return;
    }
    _build(parent, query);
}

void GeneratorLoadSql::buildSingle(ComponentData* parent, const QString& id)
{
    buildCallCount[LoadSql]++;

    QByteArray sql = _sql(parent);
    QSqlQuery query;
    sql += " AND id=:id"; //TODO: des passt sicha ned imma
    query.bindValue(":id", id);
    if (!query.exec(sql)) {
        qCritical() << "can't execute query GeneratorLoadSql::build" << query.lastError() << query.executedQuery();
        Q_ASSERT(0);
        return;
    }
    _build(parent, query);
}

void GeneratorLoadSql::refresh(ComponentData* d)
{
    ComponentData* parent = d->parent();

    QByteArray sql = _sql(parent);
    QSqlQuery query;
    sql += " AND id=:id"; //TODO: des passt sicha ned imma
    query.bindValue(":id", d->childId());
    query.exec(sql);
    if (query.next()) {
        d->setName(query.value(1).toString());
        //TODO: maxNameLength, uniqueFilename, maxFilenameLength
        //d->setFilename(query.value(2).toString());
        if (d->filename().isEmpty()) {
            d->setFilename(d->name());
        }
    }
}

QList<ComponentClass> GeneratorLoadSql::childComponentClasses()
{
    QList<ComponentClass> ret;
    ret << component;
    return ret;
}

QList<IndexedString> GeneratorLoadSql::childComponentKeys()
{
    QList<IndexedString> ret;
    ret << key;
    return ret;
}


QByteArray GeneratorLoadSqlWithComponent::_sql(ComponentData* parent)
{
    QList<QByteArray> arg;
    arg << QByteArray("--componentId=") + parent->componentId().toUtf8();
    arg << QByteArray("--generator=") + key.toString().toUtf8();
    return PhpProcess::getInstance()->call("generator-sql", arg);
}

void GeneratorLoadSqlWithComponent::_build(ComponentData* parent, QSqlQuery query)
{
    while (query.next()) {
        int id = query.value(0).toInt();
        ComponentData *d = new ComponentData(this, parent, idSeparator, id, component[query.value(1).toString()]);
        d->setDbIdPrefix(dbIdPrefix);
        d->setFilename("todo");
        d->setName("todo");
        parent->addChildren(d);
    }

}


void GeneratorLoadSqlWithComponent::build(ComponentData* parent)
{
    buildCallCount[LoadSqlWithComponent]++;
    ifDebugGeneratorBuild( qDebug() << "GeneratorLoadSqlWithComponent::build" << parent->componentId() << sql; )

    QByteArray sql = _sql(parent);
    if (sql.isEmpty()) return;
    QSqlQuery query;
    if (!query.exec(sql)) {
        qCritical() << "can't execute query GeneratorLoadSqlWithComponent::build" << query.lastError() << query.executedQuery();
        Q_ASSERT(0);
        return;
    }
    _build(parent, query);
}

void GeneratorLoadSqlWithComponent::buildSingle(ComponentData* parent, const QString& id)
{
    buildCallCount[LoadSqlWithComponent]++;

    QByteArray sql = _sql(parent);
    QSqlQuery query;
    sql += " AND id=:id"; //TODO: des passt sicha ned imma
    query.bindValue(":id", id);
    if (!query.exec(sql)) {
        qCritical() << "can't execute query GeneratorLoadSqlWithComponent::build" << query.lastError() << query.executedQuery();
        Q_ASSERT(0);
        return;
    }
    _build(parent, query);
}

void GeneratorLoadSqlWithComponent::refresh(ComponentData* d)
{
    ComponentData* parent = d->parent();

    QByteArray sql = _sql(parent);
    QSqlQuery query;
    sql += " AND id=:id"; //TODO: des passt sicha ned imma
    query.bindValue(":id", d->childId());
    query.exec(sql);
    if (query.next()) {
        int id = query.value(0).toInt();
        if (d->componentClass() != component[query.value(1).toString()]) {
            parent->childrenLock()->lockForWrite();
            delete d;
            buildSingle(parent, QString::number(id));
            parent->childrenLock()->unlock();
        } else {
            //TODO update name & filename (sind oben auch noch todo)
        }
    }
}

QList<ComponentClass> GeneratorLoadSqlWithComponent::childComponentClasses()
{
    return component.values();
}

QList<IndexedString> GeneratorLoadSqlWithComponent::childComponentKeys()
{
    return component.keys();
}

void GeneratorLoad::build(ComponentData* parent)
{
    buildCallCount[Load]++;
    ifDebugGeneratorBuild( qDebug() << "GeneratorLoad::build" << parent->componentId(); )
    QList<QByteArray> arg;
    arg << QByteArray("--componentId=") + parent->componentId().toUtf8();
    arg << QByteArray("--generator=") + key.toString().toUtf8();
    _build(parent, arg);
}

void GeneratorLoad::buildSingle(ComponentData* parent, const QString& id)
{
    buildCallCount[Load]++;
    ifDebugGeneratorBuild( qDebug() << "GeneratorLoad::build" << parent->componentId(); )
    QList<QByteArray> arg;
    arg << QByteArray("--componentId=") + parent->componentId().toUtf8();
    arg << QByteArray("--generator=") + key.toString().toUtf8();
    arg << QByteArray("--id=") + id.toUtf8();
    _build(parent, arg);
}

void GeneratorLoad::refresh(ComponentData* d)
{
    ComponentData* parent = d->parent();
    QString id = d->childId();

    QList<QByteArray> arg;
    arg << QByteArray("--componentId=") + parent->componentId().toUtf8();
    arg << QByteArray("--generator=") + key.toString().toUtf8();
    arg << QByteArray("--id=") + id.toUtf8();

    QXmlStreamReader xml(PhpProcess::getInstance()->call("get-child-components", arg));
    while (!xml.atEnd()) {
        xml.readNext();
        if (xml.isStartElement() && xml.name() == "component") {
            while (!xml.atEnd()) {
                xml.readNext();
                if (xml.isStartElement() && xml.name() == "componentClass") {
                    ComponentClass cc = ComponentClass(IndexedString(xml.readElementText()));
                    if (cc != d->componentClass()) {
                        parent->childrenLock()->lockForWrite();
                        delete d;
                        buildSingle(parent, id);
                        parent->childrenLock()->unlock();
                        break;
                    }
                }
            }
        }
    }
}

QList<ComponentData*> GeneratorLoad::_build(ComponentData* parent, QList<QByteArray> args)
{
    QList<ComponentData*> ret;

    PhpProcess *p = PhpProcess::getInstance();
    ifDebugGeneratorBuild( qDebug() << "get-child-components" << args; )
    QXmlStreamReader xml(p->call("get-child-components", args));
    while (!xml.atEnd()) {
        xml.readNext();
        if (xml.isStartElement() && xml.name() == "component") {
            QString componentId;
            QString dbId;
            ComponentClass componentClass;
            QString fn;
            QString name;
            bool isHome = false;
            while (!xml.atEnd()) {
                xml.readNext();
                if (xml.isStartElement() && xml.name() == "componentId") {
                    componentId = xml.readElementText();
                }
                if (xml.isStartElement() && xml.name() == "dbId") {
                    dbId = xml.readElementText();
                }
                if (xml.isStartElement() && xml.name() == "componentClass") {
                    componentClass = ComponentClass(IndexedString(xml.readElementText()));
                }
                if (xml.isStartElement() && xml.name() == "filename") {
                    fn = xml.readElementText();
                }
                if (xml.isStartElement() && xml.name() == "name") {
                    name = xml.readElementText();
                }
                if (xml.isStartElement() && xml.name() == "isHome") {
                    isHome = (bool)xml.readElementText().toInt();
                }
                if (xml.isEndElement() && xml.name() == "component") break;
            }
            ComponentData *d = new ComponentData(this, parent, componentId, dbId, componentClass);

            if (componentTypes & TypePseudoPage) {
                if (!fn.isEmpty()) {
                    d->setFilename(fn);
                }
                d->setIsHome(isHome);
            }
            if (componentTypes & TypePage) {
                if (!name.isEmpty()) {
                    d->setName(name);
                }
            }

            ret << d;
            parent->addChildren(d);
        }
    }
    if (xml.hasError()) {
        qFatal(xml.errorString().toAscii().data());
    }

    return ret;
}

QList<ComponentClass> GeneratorLoad::childComponentClasses()
{
    return component.values();
}

QList<IndexedString> GeneratorLoad::childComponentKeys()
{
    return component.keys();
}

void GeneratorPages::build(ComponentData* parent)
{
    buildCallCount[Pages]++;
    ifDebugGeneratorBuild( qDebug() << "GeneratorPages::build" << parent->componentId(); )
    QList<QByteArray> arg;
    arg << QByteArray("--componentId=") + parent->componentId().toUtf8();
    arg << QByteArray("--pageGenerator=1");
    QList<ComponentData*> pages = _build(parent, arg);

    //pages-generator ist eigentlich rekursiv
    foreach (ComponentData *d, pages) {
        d->m_childrenLock.lockForWrite();
        build(d);
        d->m_childrenLock.unlock();
    }
}

bool GeneratorPages::showInMenu(ComponentData* d)
{
    Q_ASSERT(d->generator() == this);
    fetchRowData(d->parent(), IndexedString("hide"));
    return !(bool)d->rowData[IndexedString("hide")].toInt();
}

void GeneratorLinkTag::preload()
{
    QSqlQuery query;
    QString sql = "SELECT component_id, component FROM vpc_basic_linktag";
    if (!query.exec(sql)) {
        qCritical() << "can't execute query GeneratorLinkTag::preload" << query.lastError();
        Q_ASSERT(0);
        return;
    }
    componentIdToComponent.clear();
    while (query.next()) {
        componentIdToComponent[query.value(0).toString()] = query.value(1).toString();
    }
}

void GeneratorLinkTag::build(ComponentData* parent)
{
    buildCallCount[LinkTag]++;
    ifDebugGeneratorBuild( qDebug() << "GeneratorLinkTag::build" << parent->componentId(); )
    ComponentClass c = component[componentIdToComponent[parent->dbId()]];
    if (!c.isEmpty()) { //TODO warum tritt das auf - sollte nicht sein
        ComponentData *d = new ComponentData(this, parent, idSeparator, IndexedString("link"), c);
        parent->addChildren(d);
    }
}

void GeneratorLinkTag::buildSingle(ComponentData* parent, const QString& id)
{
    Q_UNUSED(parent);
    Q_UNUSED(id);
    Q_ASSERT(0);
}

void GeneratorLinkTag::refresh(ComponentData* d)
{
    Q_UNUSED(d);
    Q_ASSERT(0); //TODO: not yet implemented, component can be changed
}

QList< ComponentClass > GeneratorLinkTag::childComponentClasses()
{
    return component.values();
}


QList<IndexedString> GeneratorLinkTag::childComponentKeys()
{
    return component.keys();
}


