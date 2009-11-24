#include <QSqlQuery>
#include <QSqlError>
#include <QXmlStreamReader>
#include <QBuffer>
#include <QTime>

#include "PhpProcess.h"
#include "Generator.h"
#include "ComponentData.h"
#include "Unserializer.h"
#include "Model.h"

#define ifDebugGeneratorBuild(x) x
#define ifDebugGeneratorBuildInherit(x)
#define ifDebugBuildWithGenerators(x) x
#define ifDebugPriority(x)
#define ifDebugHandleChangedRow(x) x

#include "ComponentDataRoot.h"

QHash<const ComponentDataRoot *, QList<Generator*> > Generator::m_generators;
QHash<Generator::Type, int> Generator::buildCallCount;
QHash<const ComponentDataRoot*, QList<Generator*> > Generator::m_inheritGeneratorsCache;

Generator::~Generator()
{
    Q_ASSERT(builtComponents.isEmpty());
    BuildOnlyPagesGeneratorStrategy::m_cache.remove(this);
    BuildWithDbIdShortcutStrategy::m_cache.remove(this);
    Model::m_instancesGenerator.remove(this);
}

void Generator::deleteGenerators(const ComponentDataRoot* root)
{
    qDeleteAll(Generator::m_generators[root]);
    m_generators.remove(root);
    m_inheritGeneratorsCache.remove(root);
}

QList<Generator*> Generator::inheritGenerators(const ComponentDataRoot* root)
{
    static QHash<const ComponentDataRoot*, QList<Generator*> > m_inheritGeneratorsCache;
    if (!m_inheritGeneratorsCache.contains(root)) {
        Q_ASSERT(m_generators.contains(root));
        m_inheritGeneratorsCache[root] = QList<Generator*>();
        foreach (Generator *g, m_generators[root]) {
            if (g->generatorFlags & Generator::TypeInherit) {
                m_inheritGeneratorsCache[root] << g;
            }
        }
    }
    return m_inheritGeneratorsCache[root];
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
QHash<Generator *, bool> BuildOnlyPagesGeneratorStrategy::m_cache;
bool BuildOnlyPagesGeneratorStrategy::canHavePagesGeneratorAsChild(Generator *generator) const
{
    if (m_cache.contains(generator)) return m_cache[generator];

    m_cache[generator] = false;

    if (generator->generatorFlags & Generator::TypePagesGenerator) {
        m_cache[generator] = true;
        return true;
    }

    foreach (ComponentClass c, generator->childComponentClasses()) {
        foreach (Generator *g, Generator::generators(generator->root())) {
            if (g->componentClass == c) {
                if (canHavePagesGeneratorAsChild(g)) {
                    m_cache[generator] = true;
                    return true;
                }
            }
        }
    }
    return false;
}

bool BuildOnlyPagesGeneratorStrategy::skip(ComponentData *parent) const
{
    foreach (Generator *g, Generator::generators(parent->root())) {
        if (parent->componentClass() == g->componentClass) {
            if (canHavePagesGeneratorAsChild(g)) {
                return false;
            }
        }
    }
    return true;
}


QHash<Generator *, BuildWithDbIdShortcutStrategy::DbIdShortcutType> BuildWithDbIdShortcutStrategy::m_cache;
BuildWithDbIdShortcutStrategy::DbIdShortcutType BuildWithDbIdShortcutStrategy::canHaveDbIdShortcutAsChild(Generator *generator) const
{
    if (m_cache.contains(generator)) return m_cache[generator];

    m_cache[generator] = NoDbIdShortcut;

    if (!generator->dbIdPrefix.isEmpty()) {
        m_cache[generator] = DirectDbIdShortcut;
        return DirectDbIdShortcut;
    }

    foreach (ComponentClass c, generator->childComponentClasses()) {
        foreach (Generator *g, Generator::generators(generator->root())) {
            if (g->componentClass == c) {
                if (canHaveDbIdShortcutAsChild(g) != NoDbIdShortcut) {
                    m_cache[generator] = IndirectDbIdShortcut;
                    return IndirectDbIdShortcut;
                }
            }
        }
    }
    return NoDbIdShortcut;
}
bool BuildWithDbIdShortcutStrategy::skip(ComponentData* parent) const
{
    foreach (Generator *g, Generator::generators(parent->root())) {
        if (parent->componentClass() == g->componentClass) {
            if (canHaveDbIdShortcutAsChild(g) != NoDbIdShortcut) {
                return false;
            }
        }
    }
    return true;
}


void Generator::buildWithGenerators(ComponentData* parent, const BuildStrategy *buildStrategy)
{
    ifDebugBuildWithGenerators( qDebug() << "buildWithGenerators parent:" << parent->componentId(); )
    if (buildStrategy && buildStrategy->skip(parent)) {
        ifDebugBuildWithGenerators( qDebug() << "SKIP"; )
        return;
    }

    parent->childrenLock()->lockForRead();

    bool childrenBuilt = parent->m_childrenBuilt;
    ifDebugBuildWithGenerators( qDebug() << "childrenBuilt" << childrenBuilt; )

    if (!childrenBuilt) {
        parent->childrenLock()->unlock();
        parent->childrenLock()->lockForWrite();

        parent->m_childrenBuilt = true;

        foreach (Generator *g, generators(parent->root())) {
            if (g->componentClass == parent->componentClass()) {
                g->build(parent);
            }
        }

        //build inherited children
        if (parent->generator() && parent->generator()->generatorFlags & Generator::TypeInherits) {
            ComponentData *p = parent;
            while ((p = p->parent())) {
                foreach (Generator *g, inheritGenerators(parent->root())) {
                    if (!(g->generatorFlags & Generator::TypeUnique) && g->componentClass == p->componentClass()) {
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

    if (!childrenBuilt && parent->generator() && parent->generator()->generatorFlags & Generator::TypeInherits) {
        ifDebugGeneratorBuildInherit( qDebug() << "----->inherits" << parent->generator()->componentClass << parent->generator()->key << parent->componentId(); )
        ComponentData *p = parent;
        while ((p = p->parent())) {
            ifDebugGeneratorBuildInherit( qDebug() << "check if inherits us something" << p->componentId(); )
            foreach (Generator *g, inheritGenerators(parent->root())) {
                if (g->generatorFlags & Generator::TypeUnique && g->componentClass == p->componentClass()) {
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
                        ifDebugPriority(
                            if (c->priority() == c2->priority()) {
                                //qDebug() << "same priority" << c->componentId() << c2->componentId() << c2->treeLevel();
                            }
                        )
                        if (c->priority() > c2->priority()
                            || (c->priority() == c2->priority() && c->treeLevel() <= c2->treeLevel())) {
                            parent->m_children.removeAt(i2);
                            Q_ASSERT(i2 != i);
                            if (i2 < i) {
                                i--;
                            }
                            i2--;
                            ifDebugPriority( qDebug() << "DELETE c2>>>" << c2 << c2->componentId() << "not" << c->componentId(); )
                            if (!(c2->generator()->generatorFlags & Generator::TypeInherit)) {
                                //TODO: leaked, manchmal muss auch in inherit gelöscht werden (kann vielleicht woanders gelöst werden)
                                //wurde aber eigentlich schon gelöst indem es mit dem generator mitgelöscht wird falls der gelöscht wird
                                //(bei einem reset aufruf)
                                delete c2;
                            }
                        } else {
                            qDebug() << parent->m_children;
                            parent->m_children.removeAt(i);
                            i--;
                            ifDebugPriority( qDebug() << "DELETE c>>>" << c << c->componentId() << "not" << c2->componentId(); )
                            qDebug() << parent->m_children;
                            if (!(c->generator()->generatorFlags & Generator::TypeInherit)) {
                                //TODO: leaked, manchmal muss auch in inherit gelöscht werden (kann vielleicht woanders gelöst werden)
                                //wurde aber eigentlich schon gelöst indem es mit dem generator mitgelöscht wird falls der gelöscht wird
                                //(bei einem reset aufruf)
                                delete c;
                            }
                            break;
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
    ifDebugHandleChangedRow( qDebug() << "Generator::handleChangedRow" << method << model << id; )
    switch (method) {
        case RowUpdated:
            foreach (Generator *g, generators()) {
                if (g->model == model) {
                    ifDebugHandleChangedRow( qDebug() << "refresh" << g->model << g->componentClass << g->key; )
                    g->preload(); //tut im moment noch _alles_ preloaden, möglicherweise nur eine id preloaden wenn zu langsam
                    foreach (ComponentData *d, g->builtComponents) { //das benötigt womöglich einen index wenns zu langsam ist
                        ifDebugHandleChangedRow( qDebug() << d->componentId() << d->childId() << id; )
                        if (d->childId() == id) {
                            ifDebugHandleChangedRow( qDebug() << "refresh" << d->componentId() << d->rowData.keys(); )
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
            foreach (Generator *g, generators()) {
                if (g->model == model) {
                    ifDebugHandleChangedRow( qDebug() << "found generator" << g->componentClass << g->key; )
                    g->preload(); //tut im moment noch _alles_ preloaden, möglicherweise nur eine id preloaden wenn zu langsam
                    foreach (ComponentData *d, ComponentData::getComponentsByClass(g->root(), g->componentClass)) {
                        QWriteLocker locker(&d->m_childrenLock);
                        if (d->m_childrenBuilt) {
                            ifDebugHandleChangedRow( qDebug() << "buildSingle" << d->componentId() << id; )
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
            foreach (Generator *g, generators()) {
                if (g->model == model) {
                    foreach (ComponentData *d, g->builtComponents) { //das benötigt womöglich einen index wenns zu langsam ist
                        if (d->childId() == id) {
                            Q_ASSERT(!(d->generator()->generatorFlags & Generator::TypeInherit));
                            if (!(d->generator()->generatorFlags & Generator::TypeInherit)) {
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
    if (!onlyId.isEmpty()) {
        Q_ASSERT(parent->m_fetchedRowData.contains(field));
    } else {
        if (parent->m_fetchedRowData.contains(field)) return;
        parent->m_fetchedRowData.insert(field);
    }

    QHash<int, ComponentData*> childIds = parent->childIdsHash();
    int fetchedButNotUsed = 0;
    
    QHash<IndexedString, IndexedString> fields;
    fields[IndexedString("field")] = field;
    Model::RowSet rows = Model::instance(model)->fetchRows(fields, onlyId);
    if (!onlyId.isEmpty()) {
        Q_ASSERT(rows.count() <= 1);
    }
    foreach (const Model::Row &row, rows) {
        int id = row.id().toInt();
        qDebug() << "fetchRowData" << id << row.value(IndexedString("field"));
        if (childIds.contains(-id)) {
            childIds[-id]->rowData[field] = row.value(IndexedString("field"));
        } else if (childIds.contains(id)) {
            childIds[id]->rowData[field] = row.value(IndexedString("field"));
        }
        if (!childIds.contains(-id) && !childIds.contains(id)) {
            fetchedButNotUsed++;
        }
    }
    if (fetchedButNotUsed) {
        qWarning() << fetchedButNotUsed << "rows fetched but not used";
    }
}


QList<int> GeneratorWithModel::fetchIds(ComponentData* parent, const Select& select) const
{
    Q_UNUSED(parent); //TODO: kÃ¶nnte verwendet werden um nicht zuviel zu fetchen

    QList<QByteArray> args;
    args << QByteArray("--model=") + model.toString().toUtf8();
    args << QByteArray("--select=") + serialize(select).replace('\0', "\\0");

    QTime stopWatch;
    stopWatch.start();
    QByteArray data = PhpProcess::getInstance()->call(parent->root(), "model-get-ids", args);
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
    if (!(generatorFlags & ColumnVisible)) {
        return true;
    }
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
    return (d->generatorFlags() & Generator::TypeShowInMenu);
}

bool Generator::isVisible(const ComponentData* d) const
{
    Q_UNUSED(d);
    return true;
}

QList<QString> Generator::tags(const ComponentData* d) const
{
    Q_UNUSED(d);
    return QList<QString>();
}

void GeneratorStatic::build(ComponentData* parent)
{
    buildCallCount[Static]++;
    ifDebugGeneratorBuild( qDebug() << "GeneratorStatic::build" << componentClass << key << parent->componentId();)
    QHashIterator<IndexedString, ComponentClass> i(component);
    while (i.hasNext()) {
        i.next();
        ComponentData *d = new ComponentData(this, parent, idSeparator, i.key(), i.value());
        if (generatorFlags & TypePseudoPage) {
            QString fn = filename;
            if (fn.isEmpty()) {
                fn = name;
            }
            if (fn.isEmpty()) {
                fn = i.key().toString();
            }
            d->setFilename(fn);
        }
        if (generatorFlags & TypePage) {
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
/*
void GeneratorTable::_build(ComponentData* parent, QString onlyId)
{
    QHash<IndexedString, IndexedString> fields;
    fields[IndexedString("name")] = IndexedString("--generator-name");
    fields[IndexedString("filename")] = IndexedString("--generator-filename");
    if (generatorFlags & ColumnComponentId) {
        fields[IndexedString("component_id")] = IndexedString("component_id");
    }
    Model::RowSet rows = Model::instance(this)->fetchRows(fields, parent, onlyId);
    if (!onlyId.isEmpty()) {
        Q_ASSERT(rows.count() <= 1);
    }
    foreach (const Model::Row &row, rows) {
        if (generatorFlags & ColumnComponentId) {
            if (parent->dbId() != row.value(IndexedString("component_id")).toString()) continue;
        }
        ComponentData *d = new ComponentData(this, parent, idSeparator, row.id(), component);
        d->setDbIdPrefix(dbIdPrefix);
        d->setName(row.value(IndexedString("name")).toString());
        d->setFilename(row.value(IndexedString("filename")).toString());
        parent->addChildren(d);
    }
}
*/
void GeneratorTable::_build(ComponentData* parent, QString onlyId)
{
    QHash<IndexedString, IndexedString> fields;
    fields[IndexedString("name")] = IndexedString("--generator-name");
    fields[IndexedString("filename")] = IndexedString("--generator-filename");
    if (generatorFlags & ColumnComponent) {
        fields[IndexedString("component")] = IndexedString("--generator-component");
    }
    if (generatorFlags & ColumnComponentId) {
        fields[IndexedString("component_id")] = IndexedString("component_id");
    }
    Model::RowSet rows = Model::instance(this)->fetchRows(fields, parent, onlyId);
    if (!onlyId.isEmpty()) {
        Q_ASSERT(rows.count() <= 1);
    }
    foreach (const Model::Row &row, rows) {
        if (generatorFlags & ColumnComponentId) {
            if (parent->dbId() != row.value(IndexedString("component_id")).toString()) continue;
        }
        ComponentClass c;
        if (generatorFlags & ColumnComponent) {
            c = component[row.value(IndexedString("component")).toString()];
        } else {
            c = component[key];
        }
        ComponentData *d = new ComponentData(this, parent, idSeparator,
                    IndexedString(row.id()),
                    c);
        d->setDbIdPrefix(dbIdPrefix);
        d->setName(row.value(IndexedString("name")).toString());
        d->setFilename(row.value(IndexedString("filename")).toString());
        parent->addChildren(d);
    }
}

void GeneratorTable::build(ComponentData* parent)
{
    buildCallCount[Table]++;
    ifDebugGeneratorBuild( qDebug() << "GeneratorTable::build" << parent->componentId(); )
    _build(parent);
}

void GeneratorTable::buildSingle(ComponentData* parent, const QString& id)
{
    buildCallCount[Table]++;
    ifDebugGeneratorBuild( qDebug() << "GeneratorTable::buildSingle" << parent->componentId(); )
    _build(parent, id);
}

void GeneratorTable::refresh(ComponentData* d)
{
    Q_UNUSED(d);
    //TODO!!!
}

QList<ComponentClass> GeneratorTable::childComponentClasses()
{
    return component.values();
}

QList<IndexedString> GeneratorTable::childComponentKeys()
{
    return component.keys();
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
    if (generatorFlags & ColumnComponentId) {
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
    if (generatorFlags & ColumnComponentId) {
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
    if (generatorFlags & ColumnComponentId) {
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
        if (generatorFlags & ColumnComponentId) {
            componentId = query.value(2).toString();
        }
        data[componentId] << qMakePair(query.value(0).toInt(), component[IndexedString(query.value(1).toString())]);
    }
}

QList< QPair< int, ComponentClass > > GeneratorTableSqlWithComponent::_items(ComponentData* parent)
{
    QList<QPair<int, ComponentClass> > items;
    if (generatorFlags & ColumnComponentId) {
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
    qDebug() << "GeneratorTableSqlWithComponent::refresh" << d->componentId();

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
    Q_ASSERT(generatorFlags & ColumnComponentId);
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

QByteArray GeneratorLoadSql::_sql(ComponentData* parent, int id)
{
    QList<QByteArray> arg;
    arg << QByteArray("--componentId=") + parent->componentId().toUtf8();
    arg << QByteArray("--generator=") + key.toString().toUtf8();
    if (id) {
        arg << QByteArray("--id=") + QByteArray::number(id);
    }
    qDebug() << "generator-sql" << arg;
    return PhpProcess::getInstance()->call(parent->root(), "generator-sql", arg);
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
    QByteArray sql = _sql(parent);
    ifDebugGeneratorBuild( qDebug() << "GeneratorLoadSql::build" << parent->componentId() << sql; )
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

    QByteArray sql = _sql(parent, id.toInt());
    QSqlQuery query;
    if (!query.exec(sql)) {
        qCritical() << "can't execute query GeneratorLoadSql::build" << query.lastError() << query.executedQuery();
        Q_ASSERT(0);
        return;
    }
    _build(parent, query);
}

void GeneratorLoadSql::refresh(ComponentData* d)
{
    qDebug() << "GeneratorLoadSql::refresh" << d->componentId();

    ComponentData* parent = d->parent();

    QByteArray sql = _sql(parent, d->childId().toInt());
    qDebug() << sql;
    QSqlQuery query;
    if (!query.exec(sql)) {
        qCritical() << "can't execute query GeneratorLoadSql::refresh" << query.lastError() << query.executedQuery();
        Q_ASSERT(0);
        return;
    }
    if (query.next()) {
        qDebug() << "new name" << query.value(1).toString();
        d->setName(query.value(1).toString());
        //TODO: maxNameLength, uniqueFilename, maxFilenameLength
        //d->setFilename(query.value(2).toString());
        //if (d->filename().isEmpty()) {
            d->setFilename(d->name());
        //}
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
    return PhpProcess::getInstance()->call(parent->root(), "generator-sql", arg);
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

    QByteArray sql = _sql(parent);
    ifDebugGeneratorBuild( qDebug() << "GeneratorLoadSqlWithComponent::build" << parent->componentId() << sql; )
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
    qDebug() << "GeneratorLoadSqlWithComponent::refresh" << d->componentId();

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
    qDebug() << "GeneratorLoad::refresh" << d->componentId();

    ComponentData* parent = d->parent();
    QString id = d->childId();

    QList<QByteArray> arg;
    arg << QByteArray("--componentId=") + parent->componentId().toUtf8();
    arg << QByteArray("--generator=") + key.toString().toUtf8();
    arg << QByteArray("--id=") + id.toUtf8();

    QXmlStreamReader xml(PhpProcess::getInstance()->call(d->root(), "get-child-components", arg));
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
    QXmlStreamReader xml(p->call(parent->root(), "get-child-components", args));
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

            if (generatorFlags & TypePseudoPage) {
                if (!fn.isEmpty()) {
                    d->setFilename(fn);
                }
                d->setIsHome(isHome);
            }
            if (generatorFlags & TypePage) {
                if (!name.isEmpty()) {
                    d->setName(name);
                }
            }

            ret << d;
            parent->addChildren(d);
        }
    }
    if (xml.hasError()) {
        qWarning() << parent->root()->componentClass() << "get-child-components" << args;
        qWarning() << xml.errorString();
        Q_ASSERT(0);
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


QList<QString> GeneratorPages::tags(const ComponentData* d) const
{
    Q_ASSERT(d->generator() == this);
    const_cast<GeneratorPages*>(this)->fetchRowData(d->parent(), IndexedString("tags"));
    return d->rowData[IndexedString("tags")].toString().split(',');
}


void GeneratorLinkTag::preload()
{
    QHash<IndexedString, IndexedString> fields;
    fields[IndexedString("component")] = IndexedString("component");
    Model::RowSet rows = Model::instance(model)->fetchRows(fields, Select());
    componentIdToComponent.clear();
    foreach (const Model::Row &row, rows) {
        componentIdToComponent[row.id()] = row.value(IndexedString("component")).toString();
    }
}

void GeneratorLinkTag::build(ComponentData* parent)
{
    buildCallCount[LinkTag]++;
    ifDebugGeneratorBuild( qDebug() << "GeneratorLinkTag::build" << parent->componentId(); )
    if (!componentIdToComponent.contains(parent->dbId())) {
        //TODO: das dürfte eigentlich nicht passieren, kann es jedoch wenn eine row
        //noch nie gespeichert wurde; korrekt wäre da die defaultValues zu verwenen
        return;
    }
    ComponentClass c = component[componentIdToComponent[parent->dbId()]];
    qDebug() << "GeneratorLinkTag::build" << parent->componentId() << c;
    ComponentData *d = new ComponentData(this, parent, idSeparator, IndexedString("link"), c);
    parent->addChildren(d);
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

void Generator::createGenerators(const ComponentDataRoot *root)
{
    PhpProcess *p = PhpProcess::getInstance();
    QXmlStreamReader xml(p->call(root, "generators"));
    while (!xml.atEnd()) {
        xml.readNext();
        if (xml.isStartElement() && xml.name() == "generator") {
            Generator::Type type = Generator::Unknown;
            Generator *g;
            QString t = xml.attributes().value("type").toString();
            if (t == "static") {
                type = Generator::Static;
                g = new GeneratorStatic(root);
            } else if (t == "table") {
                type = Generator::Table;
                g = new GeneratorTable(root);
            } else if (t == "tableSql") {
                type = Generator::TableSql;
                g = new GeneratorTableSql(root);
            } else if (t == "load") {
                type = Generator::Load;
                g = new GeneratorLoad(root);
            } else if (t == "pages") {
                type = Generator::Pages;
                g = new GeneratorPages(root);
            } else if (t == "tableSqlWithComponent") {
                type = Generator::TableSqlWithComponent;
                g = new GeneratorTableSqlWithComponent(root);
            } else if (t == "loadSql") {
                type = Generator::LoadSql;
                g = new GeneratorLoadSql(root);
            } else if (t == "loadSqlWithComponent") {
                type = Generator::LoadSqlWithComponent;
                g = new GeneratorLoadSqlWithComponent(root);
            } else if (t == "linkTag") {
                type = Generator::LinkTag;
                g = new GeneratorLinkTag(root);
            } else {
                Q_ASSERT(0);
            }
            g->componentClass = ComponentClass(xml.attributes().value("componentClass").toString());

            ComponentClass component;
            QHash<IndexedString, ComponentClass> components;
            QString sql;
            QString tableName;
            while (!xml.atEnd()) {
                if (xml.isStartElement() && xml.name() == "key") {
                    g->key = IndexedString(xml.readElementText());
                }
                if (xml.isStartElement() && xml.name() == "class") {
                    g->generatorClass = IndexedString(xml.readElementText());
                }
                if (xml.isStartElement() && xml.name() == "parentClass") {
                    g->parentClasses << IndexedString(xml.readElementText());
                }
                if (xml.isStartElement() && xml.name() == "box") {
                    g->box = IndexedString(xml.readElementText());
                }
                if (xml.isStartElement() && xml.name() == "priority") {
                    g->priority = xml.readElementText().toInt();
                }
                if (xml.isStartElement() && xml.name() == "model") {
                    g->model = IndexedString(xml.readElementText());
                }
                if (xml.isStartElement() && xml.name() == "uniqueFilename") {
                    g->uniqueFilename = xml.readElementText().toInt();
                }
                if (xml.isStartElement() && xml.name() == "component") {
                    if (!xml.attributes().value("key").isEmpty()) {
                        Q_ASSERT(component.isEmpty());
                        IndexedString k = IndexedString(xml.attributes().value("key").toString());
                        QString c = xml.readElementText();
                        Q_ASSERT(!c.isEmpty());
                        components[k] = ComponentClass(c);
                    } else {
                        Q_ASSERT(components.isEmpty());
                        QString c = xml.readElementText();
                        Q_ASSERT(!c.isEmpty());
                        component = ComponentClass(c);
                    }
                }
                if (xml.isStartElement() && xml.name() == "idSeparator") {
                    QString s = xml.readElementText();
                    if (s == QString('-')) {
                        g->idSeparator = Generator::Dash;
                    } else if (s == QString('_')) {
                        g->idSeparator = Generator::Underscore;
                    }
                }
                if (xml.isStartElement() && xml.name() == "sql") {
                    sql = xml.readElementText();
                }
                if (xml.isStartElement() && xml.name() == "tableName") {
                    tableName = xml.readElementText();
                }
                if (xml.isStartElement() && xml.name() == "pageGenerator") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->generatorFlags |= Generator::TypePage;
                    }
                }
                if (xml.isStartElement() && xml.name() == "boxGenerator") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->generatorFlags |= Generator::TypeBox;
                    }
                }
                if (xml.isStartElement() && xml.name() == "multiBoxGenerator") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->generatorFlags |= Generator::TypeMultiBox;
                    }
                }
                if (xml.isStartElement() && xml.name() == "pseudoPageGenerator") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->generatorFlags |= Generator::TypePseudoPage;
                    }
                }
                if (xml.isStartElement() && xml.name() == "pagesGenerator") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->generatorFlags |= Generator::TypePagesGenerator;
                    }
                }
                if (xml.isStartElement() && xml.name() == "inherit") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->generatorFlags |= Generator::TypeInherit;
                    }
                }
                if (xml.isStartElement() && xml.name() == "unique") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->generatorFlags |= Generator::TypeUnique;
                    }
                }
                if (xml.isStartElement() && xml.name() == "inherits") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->generatorFlags |= Generator::TypeInherits;
                    }
                }
                if (xml.isStartElement() && xml.name() == "showInMenu") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->generatorFlags |= Generator::TypeShowInMenu;
                    }
                }
                if (xml.isStartElement() && xml.name() == "hasComponentId") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->generatorFlags |= Generator::ColumnComponentId;
                    }
                }
                if (xml.isStartElement() && xml.name() == "hasMultipleComponents") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->generatorFlags |= Generator::ColumnComponent;
                    }
                }
                if (xml.isStartElement() && xml.name() == "hasVisible") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->generatorFlags |= Generator::ColumnVisible;
                    }
                }
                if (xml.isStartElement() && xml.name() == "dbIdShortcut") {
                    g->dbIdPrefix = IndexedString(xml.readElementText());
                }
                if (type == Generator::Static) {
                    if (xml.isStartElement() && xml.name() == "filename") {
                        static_cast<GeneratorStatic*>(g)->filename = xml.readElementText();
                    }
                    if (xml.isStartElement() && xml.name() == "name") {
                        static_cast<GeneratorStatic*>(g)->name = xml.readElementText();
                    }
                }
                if (xml.isEndElement() && xml.name() == "generator") {
                    break;
                }
                xml.readNext();
            }
            if (type == Generator::Static) {
                if (!component.isEmpty()) components[g->key] = component;
                static_cast<GeneratorStatic*>(g)->component = components;
            } else if (type == Generator::Table) {
                if (!component.isEmpty()) components[g->key] = component;
                static_cast<GeneratorTable*>(g)->component = components;
            } else if (type == Generator::TableSql) {
                static_cast<GeneratorTableSql*>(g)->tableName = tableName;
                static_cast<GeneratorTableSql*>(g)->component = component;
            } else if (type == Generator::TableSqlWithComponent) {
                static_cast<GeneratorTableSqlWithComponent*>(g)->tableName = tableName;
                Q_ASSERT(!components.isEmpty());
                static_cast<GeneratorTableSqlWithComponent*>(g)->component = components;
            } else if (type == Generator::Load) {
                if (!component.isEmpty()) components[g->key] = component;
                static_cast<GeneratorLoad*>(g)->component = components;
            } else if (type == Generator::Pages) {
                if (!component.isEmpty()) components[g->key] = component;
                static_cast<GeneratorLoad*>(g)->component = components;
            } else if (type == Generator::LoadSql) {
                Q_ASSERT(!component.isEmpty());
                static_cast<GeneratorLoadSql*>(g)->component = component;
            } else if (type == Generator::LoadSqlWithComponent) {
                Q_ASSERT(!components.isEmpty());
                static_cast<GeneratorLoadSqlWithComponent*>(g)->component = components;
            } else if (type == Generator::LinkTag) {
                Q_ASSERT(!components.isEmpty());
                static_cast<GeneratorLinkTag*>(g)->component = components;
            } else {
                continue;
            }
            qDebug() << "Generator" << g->componentClass << g->key << root->componentClass();

            g->preload();
        }
    }
    if (xml.hasError()) {
        qDebug() << "error reading generators";
        qFatal(xml.errorString().toAscii().data());
    }
}

