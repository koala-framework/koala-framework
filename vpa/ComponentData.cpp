#include "ComponentData.h"

#include <QRegExp>

#include "ComponentDataRoot.h"
#include "Generator.h"

#define ifDebugCreateComponentData(x)
#define ifDebugGetComponentById(x)
#include <QStringList>


int ComponentData::count = 0;
QHash<QString, ComponentData*> ComponentData::m_idHash;
QMultiHash<IndexedString, ComponentData*> ComponentData::m_dbIdHash;
QHash<ComponentClass, QList<ComponentData*> > ComponentData::m_componentClassHash;
QSet<ComponentClass> ComponentData::m_componentsByClassRequested;
QList<ComponentData*> ComponentData::m_homes;

ComponentData::ComponentData(Generator* generator, ComponentData* parent_, QString componentId_, QString dbId_, ComponentClass componentClass_)
    : parent(parent_), m_componentClass(componentClass_), m_generator(generator)
{
    if (parent && componentId_.startsWith(parent->componentId())) {
        componentId_ = componentId_.mid(parent->componentId().length());
        if (componentId_.startsWith('-')) {
            m_idSeparator = Generator::Dash;
        } else if (componentId_.startsWith('_')) {
            m_idSeparator = Generator::Underscore;
        } else {
            Q_ASSERT(0);
        }
        m_childId = componentId_.mid(1);
    } else {
        m_childId = componentId_;
        m_idSeparator = Generator::NoSeparator;
    }

    if (parent && dbId_.startsWith(parent->dbId())) {
        dbId_ = dbId_.mid(parent->dbId().length());
        if (dbId_.startsWith('-')) {
            Q_ASSERT(m_idSeparator == Generator::Dash);
        } else if (dbId_.startsWith('_')) {
            Q_ASSERT(m_idSeparator == Generator::Underscore);
        } else {
            Q_ASSERT(0);
        }
    } else {
        if (dbId_ != componentId()) {
            setDbIdPrefix(dbId_);
        }
    }
    init();
}

ComponentData::ComponentData(Generator* generator, ComponentData* parent_, Generator::IdSeparator separator_, int childId_, ComponentClass componentClass_)
    : parent(parent_), m_idSeparator(separator_),
      m_childId(QString::number(childId_)), m_componentClass(componentClass_),
      m_generator(generator)
{
    init();
}

ComponentData::ComponentData(Generator* generator, ComponentData* parent_, Generator::IdSeparator separator_, IndexedString childId_, ComponentClass componentClass_)
    : parent(parent_), m_idSeparator(separator_),
      m_childId(childId_.toString()), m_componentClass(componentClass_),
      m_generator(generator)
{
    init();
}

void ComponentData::init()
{
    childrenBuilt = false;

    Q_ASSERT(!m_componentClass.isEmpty());

    m_componentClassHash[m_componentClass.toIndexedString()] << this;

    ++count;
    ifDebugCreateComponentData( qDebug() << count << componentId() << componentClass().toString(); )
    if (m_idSeparator == Generator::NoSeparator) {
        if (m_idHash.contains(componentId())) {
            qWarning() << "componentId exists already" << componentId();
        }
        Q_ASSERT(!m_idHash.contains(componentId()));
        m_idHash[componentId()] = this;
    }
}


void ComponentData::buildChildren()
{
    if (childrenBuilt) return;
    qDebug() << componentId() << "buildChildren";
    static BuildNoChildrenStrategy s;
    Generator::buildWithGenerators(this, &s);
}


QList<ComponentData*> ComponentData::getComponentsByClass(ComponentClass cls)
{
    //m_componentClassHash wird in ComponentData::init geschrieben
    if (!m_componentsByClassRequested.contains(cls)) {

        m_componentsByClassRequested.insert(cls);

        if (ComponentDataRoot::getInstance()->componentClass() == cls) {
            //es darf nur eine root geben
            return m_componentClassHash[cls];
        }

        bool allSupported = true;
        QList<GeneratorTableSqlWithComponent*> generators;
        foreach (Generator *g, Generator::generators) {
            if (g->childComponentClasses().contains(cls)) {
                generators << static_cast<GeneratorTableSqlWithComponent*>(g);
                if (dynamic_cast<GeneratorTableSqlWithComponent*>(g)) {
                    //TODO: support more, by adding an interface or something
                    //(for now we are happy to get paragraphs fast
                    if (static_cast<GeneratorTableSqlWithComponent*>(g)->whereComponentId) {
                        continue;
                    }
                }
                qWarning() << "too bad" << g->generatorClass << "is only fallback spported";
                allSupported = false;
            }
        }
        if (allSupported) {
            BuildNoChildrenStrategy s;
            foreach (Generator *g, generators) {
                GeneratorTableSqlWithComponent *gt = static_cast<GeneratorTableSqlWithComponent*>(g);
                foreach (const QString &id, gt->fetchParentDbIds(cls)) {
                    foreach (ComponentData *d, ComponentDataRoot::getComponentsByDbId(id)) {
                        Generator::buildWithGenerators(d, &s);
                    }
                }
            }
        } else {
            BuildNoChildrenStrategy s;
            foreach (Generator *g, generators) {
                foreach (ComponentData *d, getComponentsByClass(g->componentClass)) {
                    Generator::buildWithGenerators(d, &s);
                }
            }
            /*
            //fallback, nicht so effizient
            BuildOnlyComponentClassStrategy s(cls);
            Generator::buildWithGenerators(ComponentDataRoot::getInstance(), &s);
            */
        }
    }
    return m_componentClassHash[cls];
}


ComponentData* ComponentData::getHome(ComponentData* subRoot)
{
    foreach (ComponentData *c, m_homes) {
        while (!subRoot->hasFlag(IndexedString("subroot"))) {
            subRoot = subRoot->parent;
            if (!subRoot) return false;
        }
        ComponentData* i = c;
        do {
            if (i == subRoot) return c;
        } while ((i = i->parent));
    }
    return 0;
}


//TODO: kopie von serialize
QHash< QByteArray, QVariant > ComponentData::dataForWeb()
{
    QHash<QByteArray, QVariant> ret;
    ret["url"] = url();
    ret["componentId"] = componentId();
    ret["dbId"] = dbId();
    if (parent) {
        ret["parentId"] = parent->componentId();
    } else {
        ret["parentId"] = false;
    }
    ret["isPage"] = QVariant(componentTypes() & Generator::TypePage);
    ret["componentClass"] = componentClass().toString();
    ret["isPseudoPage"] = QVariant(componentTypes() & Generator::TypePseudoPage);
    ret["priority"] = 0; //TODO
    ret["box"] = box().toString();
    ret["id"] = childId();
    if (generator() && !generator()->model.isEmpty()) {
        ret["model"] = generator()->model.toString();
    } else {
        ret["model"] = false;;
    }
    ret["name"] = name();
    ret["_filename"] = filename();
    ret["_rel"] = false;
    return ret;

}

//TODO: kopie von dataForWeb
QByteArray serialize(ComponentData* d)
{
    if (!d) return serialize(NullValue());
    QByteArray ret;
    QByteArray cls("Vps_Component_Data");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":14:{";
    ret += serializePrivateObjectProperty("_url", "Vps_Component_Data", d->url());
    ret += serializePrivateObjectProperty("_rel", "Vps_Component_Data", NullValue());
    ret += serializePrivateObjectProperty("_filename", "*", d->filename());
    ret += serializeObjectProperty("componentId", d->componentId());
    ret += serializeObjectProperty("dbId", d->dbId());
    if (d->parent) {
        ret += serializeObjectProperty("parentId", d->parent->componentId());
    } else {
        ret += serializeObjectProperty("parentId", NullValue());
    }
    ret += serializeObjectProperty("isPage", d->componentTypes() & Generator::TypePage);
    ret += serializeObjectProperty("componentClass", d->componentClass().toString());
    ret += serializeObjectProperty("isPseudoPage", d->componentTypes() & Generator::TypePseudoPage);
    ret += serializeObjectProperty("priority", 0); //TODO
    ret += serializeObjectProperty("box", d->box());
    ret += serializeObjectProperty("id", d->childId());
    if (d->generator() && !d->generator()->model.isEmpty()) {
        ret += serializeObjectProperty("model", d->generator()->model);
    } else {
        ret += serializeObjectProperty("model", false);
    }
    ret += serializeObjectProperty("name", d->name());
    ret += "}";
    return ret;
}

QList< ComponentData* > ComponentData::recursiveChildComponents(const Select& s, const Select& childSelect)
{
    buildChildren();

    QList<ComponentData*> ret;
    foreach (ComponentData *d, children) {
        if (s.match(d)) {
            ret << d;
        }
        if (s.limitCount && ret.count() >= (s.limitCount+s.limitOffset)) {
            break;
        }
        if (childSelect.match(d)) {
            ret << d->recursiveChildComponents(s, childSelect);
        }
    }
    if (s.limitOffset) {
        ret = ret.mid(s.limitOffset);
    }
    return ret;
}

QList< ComponentData* > ComponentData::childComponents(const Select& s)
{
    buildChildren();

    QList<ComponentData*> ret;
    foreach (ComponentData *d, children) {
        if (s.match(d)) {
            ret << d;
        }
        if (s.limitCount && ret.count() >= (s.limitCount+s.limitOffset)) {
            break;
        }
    }
    if (s.limitOffset) {
        ret = ret.mid(s.limitOffset);
    }
    return ret;
}

ComponentData* ComponentData::childPageByPath(const QString& path)
{
    Select childSelect;
    childSelect.where << new SelectExprNot(new SelectExprWhereIsPseudoPage());

    ComponentData *page = this;
    foreach (const QString &pathPart, path.split('/')) {
        //qDebug() << pathPart;
                                                                 //TODO: schönere, bessere lösung nötig
        if (page==this || page->componentClass().parentClasses().contains(IndexedString("Vpc_Root_DomainRoot_Domain_Component"))) {
            //qDebug() << "checking for shortcutUrl" << pathPart;
            ComponentClass cc = ComponentClass::componentForShortcutUrl(pathPart);
            if (!cc.isEmpty()) {
                //qDebug() << "it is a shortcutUrl" << pathPart;
                bool found = false;
                QList<ComponentData*> components = ComponentData::getComponentsByClass(cc);
                foreach (ComponentData *c, components) {
                    ComponentData *p = c;
                    do {
                        if (p == page) {
                            page = c;
                            found = true;
                            break;
                        }
                    } while ((p = p->parent));
                }
                if (!found) return 0;
                continue;
            }
        }
        Select s;
        s.where << new SelectExprWhereFilename(pathPart);
        s.where << new SelectExprWhereIsPseudoPage();
        s.limitCount = 1;
        QList<ComponentData*> pages = page->recursiveChildComponents(s, childSelect);
        if (pages.isEmpty()) {
            //qDebug() << "found nothing for " << pathPart;
            return 0;
        }
        page = pages.first();
    }

                                                                      //TODO: schönere, bessere lösung nötig
    if (page && (page==this || page->componentClass().parentClasses().contains(IndexedString("Vpc_Root_DomainRoot_Domain_Component")))) {
        //qDebug() << "looking for home" << page;
        //if (page) qDebug() << "startAt" << page->componentId();
        page = ComponentData::getHome(page);
    }

    return page;
}

int ComponentData::_getNextSeperatorPos(const QString& id) {
    int pos = -1;
    if (id.indexOf('-') != -1) pos = id.indexOf('-');
    if (id.indexOf('_') != -1) {
        if (pos == -1 || pos > id.indexOf('_')) {
            pos = id.indexOf('_');
        }
    }
    return pos;
}
ComponentData* ComponentData::getComponentById(QString id)
{
    int pos = _getNextSeperatorPos(id);
    QString mainId;
    if (pos != -1) {
        mainId = id.left(pos);
        id = id.mid(pos);
    } else {
        if (!m_idHash.contains(id)) {
            ifDebugGetComponentById( qDebug() << "not in m_idHash"; )
            return 0;
        }
        return m_idHash[id];
    }
    ifDebugGetComponentById( qDebug() << "mainId" << mainId << "restId" << id; )
    if (!m_idHash.contains(mainId)) {
        ifDebugGetComponentById( qDebug() << "not in m_idHash"; )
        return 0;
    }
    ComponentData *data = m_idHash[mainId];
    return _getChildComponent(data, id);
}

QHash<int, ComponentData*> ComponentData::childIdsHash()
{
    bool ok;
    if (m_childIdsHash.isEmpty()) {
        buildChildren();
        foreach (ComponentData *c, children) {
            int childIdInt = c->m_childId.toInt(&ok);
            if (ok) {
                m_childIdsHash[childIdInt * (c->m_idSeparator == Generator::Underscore ? -1 : 1)] = c;
            }
        }
    }
    return m_childIdsHash;
}

QList< ComponentData* > ComponentData::getComponentsByDbId(QString id)
{
    QString mainId;

    int pos = _getNextSeperatorPos(id);
    if (pos != -1) {
        mainId = id.left(pos);
        id = id.mid(pos);
    } else {
        mainId = id;
        id.clear();
    }
    ifDebugGetComponentById( qDebug() << "mainId" << mainId << "restId" << id; )
    QList<ComponentData*> ret;

    //zuerst über normale id suchen
    if (m_idHash.contains(mainId)) {
        if (!id.isEmpty()) {
            ComponentData *d = _getChildComponent(m_idHash[mainId], id);
            if (d) ret << d;
        } else {
            ret << m_idHash[mainId];
        }
    }
    ifDebugGetComponentById( qDebug() << "count found using m_idHash" << ret.count(); )

    ifDebugGetComponentById( qDebug() << m_dbIdHash; )
    //dann über dbId
    foreach (ComponentData *data, m_dbIdHash.values(IndexedString(mainId))) {
        ifDebugGetComponentById( qDebug() << "m_dbIdHash entry" << data->componentId(); )
        if (!id.isEmpty()) {
            ComponentData *d = _getChildComponent(data, id);
            if (d) {
                ifDebugGetComponentById( qDebug() << "<<<<<<<<<found>>>>>>" << d->componentId(); )
                ret << d;
            }
        } else {
            ret << data;
        }
    }
    ifDebugGetComponentById( qDebug() << "count found" << ret.count(); )
    return ret;
}

ComponentData* ComponentData::_getChildComponent(ComponentData* data, QString id)
{
    if (id.isEmpty()) return 0;
    while (!id.isEmpty()) {
        Generator::IdSeparator sep;
        if (id.left(1) == "_") {
            sep = Generator::Underscore;
        } else {
            sep = Generator::Dash;
        }
        id = id.mid(1);
        int pos = _getNextSeperatorPos(id);
        QString idPart;
        if (pos != -1) {
            idPart = id.left(pos);
            id = id.mid(pos);
        } else {
            idPart = id;
            id.clear();
        }

        ifDebugGetComponentById( qDebug() << "idPart" << idPart << "restId" << id; )

        bool found = false;

        bool ok;
        int idPartInt = idPart.toInt(&ok);
        if (ok) {
            idPartInt *= (sep == Generator::Underscore ? -1 : 1);
            if (data->childIdsHash().contains(idPartInt)) {
                data = data->childIdsHash()[idPartInt];
                found = true;
            }
        } else {
            data->buildChildren();
            foreach (ComponentData *c, data->children) {
                if (c->m_idSeparator == sep && idPart == c->m_childId) {
                    data = c;
                    found = true;
                    break;
                }
            }
        }
        if (!found) {
            ifDebugGetComponentById( qDebug() << "nothing found in children"; )
            ifDebugGetComponentById( qDebug() << "looking for" << sep << idPart << "in" << data->componentId(); )
            return 0;
        }
    }
    return data;
}


void ComponentData::setFilename(const QString& filename)
{
    m_filename = filename;
    m_filename = m_filename.toLower();
    m_filename.replace(QRegExp("[^a-z0-9]+"), "_");
}

void ComponentData::setName(const QString& name)
{
    m_name = name;
}

void ComponentData::setIsHome(bool isHome)
{
    if (isHome) {
        if (!m_homes.contains(this)) m_homes << this;
    } else {
        if (m_homes.contains(this)) m_homes.removeAll(this);
    }
}

QString ComponentData::url() const
{
    if (!(componentTypes() & Generator::TypePage)) {
        const ComponentData *p = page();
        if (p) return p->url();
        return QString();
    }

    if (isHome()) {
        return "/";
    }
    QStringList filenames;
    const ComponentData *page = this;
    do {
        if (!filenames.isEmpty() && page->hasFlag(IndexedString("shortcutUrl"))) {
            filenames.prepend(page->componentClass().shortcutUrl().toString());
            break;
        } else {

            //TODO: das ist ein schircher hack; das war früher im Data von dieser komponente
            if (page->componentClass().parentClasses().contains(IndexedString("Vpc_Root_DomainRoot_Domain_Component"))) continue;

            if (!page->filename().isEmpty()) filenames.prepend(page->filename());
        }
    } while ((page = page->parentPseudoPageOrRoot()));
    //TODO: $urlPrefix = Vps_Registry::get('config')->vpc->urlPrefix;
    //return ($urlPrefix ? $urlPrefix : '').'/'.implode('/', array_reverse(filenames));
    return "/" + filenames.join("/");
}

const ComponentData* ComponentData::pseudoPageOrRoot() const
{
    const ComponentData *page = this;
    while (page && !(page->componentTypes() & Generator::TypePseudoPage)) {
        if (page == ComponentDataRoot::getInstance()) return page;
        page = page->parent;
    }
    return page;
}

