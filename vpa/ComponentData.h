
#ifndef COMPONENTDATA_H
#define COMPONENTDATA_H

#include "IndexedString.h"
#include "ComponentClass.h"
#include "Serialize.h"
#include "Select.h"
#include "Generator.h"

class Generator;

class ComponentData
{
public:
    static int count;

    ComponentData(Generator *generator, ComponentData *parent_, QString componentId_, QString dbId_, ComponentClass componentClass_);
    ComponentData(Generator *generator, ComponentData *parent_, Generator::IdSeparator separator_, int childId_, ComponentClass componentClass_);
    ComponentData(Generator *generator, ComponentData *parent_, Generator::IdSeparator separator_, IndexedString childId_, ComponentClass componentClass_);
    void init();

    ~ComponentData();

    void setDbIdPrefix(IndexedString id)
    {
        if (id.isEmpty()) {
            m_dbIdPrefix = id;
            return;
        }
        Q_ASSERT(!id.toString().contains('-') && !id.toString().contains('_'));
        Q_ASSERT(m_idSeparator != Generator::NoSeparator);
        m_dbIdPrefix = id;
        if (!m_dbIdHash.contains(m_dbIdPrefix, parent())) {
            m_dbIdHash.insertMulti(m_dbIdPrefix, parent());
            //qDebug() << m_dbIdPrefix << parent->componentId();
        }
    }

    inline QString componentId() const
    {
        if (m_idSeparator == Generator::Dash) {
            return parent()->componentId()+'-'+m_childId;
        } else if (m_idSeparator == Generator::Underscore) {
            return parent()->componentId()+'_'+m_childId;
        } else if (m_idSeparator == Generator::NoSeparator) {
            return m_childId;
        } else {
            qWarning() << "unknown idSeparator" << m_idSeparator;
            Q_ASSERT(0);
            return QString();
        }
    }

    inline QString childIdWithSeparator() const
    {
        if (m_idSeparator == Generator::Dash) {
            return '-'+m_childId;
        } else if (m_idSeparator == Generator::Underscore) {
            return '_'+m_childId;
        } else if (m_idSeparator == Generator::NoSeparator) {
            return m_childId;
        } else {
            Q_ASSERT(0);
            return QString();
        }
    }

    QString dbId() const
    {
        QString parentId;
        if (m_dbIdPrefix.isEmpty() && parent()) {
            parentId = parent()->dbId();
        } else {
            parentId = m_dbIdPrefix.toString();
        }
        if (m_idSeparator == Generator::Dash) {
            return parentId+'-'+m_childId;
        } else if (m_idSeparator == Generator::Underscore) {
            return parentId+'_'+m_childId;
        } else if (m_idSeparator == Generator::NoSeparator) {
            return m_childId;
        } else {
            Q_ASSERT(0);
            return QString();
        }
    }

    ComponentClass componentClass() const
    {
        return m_componentClass;
    }

    inline bool hasFlag(IndexedString flag) const
    {
        Q_ASSERT(!m_componentClass.isEmpty());
        return m_componentClass.hasFlag(flag);
    }

    inline QHash<IndexedString, QVariant> flags() const
    {
        Q_ASSERT(!m_componentClass.isEmpty());
        return m_componentClass.flags();
    }

    inline Generator::ComponentTypes componentTypes() const {
        if (!m_generator) return Generator::ComponentTypes(Generator::TypeComponent); //wg. root
        return m_generator->componentTypes;
    }

    inline QString filename() const {
        return m_filename;
    }
    void setFilename(const QString &filename);

    inline QString name() const {
        return m_name;
    }
    void setName(const QString &name);

    inline bool isHome() const {
        return m_homes.contains(const_cast<ComponentData*>(this));
    }
    void setIsHome(bool isHome);

    inline Generator *generator() const {
        return m_generator;
    }

    inline QString childId() const {
        return m_childId;
    }
    inline Generator::IdSeparator idSeparator() const {
        return m_idSeparator;
    }

    inline bool isVisible() const {
        if (!m_generator) return true; //root
        return m_generator->isVisible(this);
    }

    inline IndexedString box() const
    {
        if (componentTypes() & Generator::TypeBox) {
            //TODO: das funktioniert nur für static, es gibt aber boxen eh nur von static
            if (!m_generator->box.isEmpty()) return m_generator->box;
            return IndexedString(m_childId);
        }
        return IndexedString();
    }

    inline int priority() const
    {
        if (componentTypes() & Generator::TypeBox) {
            return m_generator->priority;
        }
        return -1;
    }
    
    QList<QString> tags() const
    {
        if (m_generator) {
            return m_generator->tags(this);
        }
        return QList<QString>();
    }

    const ComponentDataRoot *root() const;

    inline const ComponentData *page() const
    {
        const ComponentData *page = this;
        while (page && !(page->componentTypes() & Generator::TypePage)) {
            page = page->parent();
        }
        return page;
    }

    inline const ComponentData *pseudoPage() const
    {
        const ComponentData *page = this;
        while (page && !(page->componentTypes() & Generator::TypePseudoPage)) {
            page = page->parent();
        }
        return page;
    }

    const ComponentData *pseudoPageOrRoot() const;

    inline const ComponentData *parentPseudoPageOrRoot() const
    {
        const ComponentData *page = pseudoPage();
        if (page && page->parent()) {
            return page->parent()->pseudoPageOrRoot();
        }
        return 0;
    }

    QString url() const;

    QList<ComponentData*> childComponents(const Select &s);
    QList<ComponentData*> recursiveChildComponents(const Select &s, const Select &childSelect);
    ComponentData *childPageByPath(const ComponentDataRoot *root, const QString &path);

    inline const QList<ComponentData*> &children() const
    {
        {
            QReadLocker locker(&m_childrenLock);
            if (!m_childrenBuilt) {
                //qDebug() << componentId() << "buildChildren";
                static BuildNoChildrenStrategy s;
                locker.unlock();
                Generator::buildWithGenerators(const_cast<ComponentData*>(this), &s);
            }
        }
        return m_children;
    }
    void addChildren(ComponentData* c);

    inline QReadWriteLock *childrenLock()
    {
        return &m_childrenLock;
    }

    inline ComponentData* parent() const
    {
        return m_parent;
    }

    inline int treeLevel() const
    {
        int ret = 0;
        const ComponentData *d = this;
        while ((d = d->parent())) {
            ++ret;
        }
        return ret;
    }

    QHash<int, ComponentData*> childIdsHash();

    QHash<QByteArray, QVariant> dataForWeb();

    QHash<IndexedString, QVariant> rowData;

    static int _getNextSeperatorPos(const QString &id);
    static ComponentData *getComponentById(const ComponentDataRoot* root, QString id);
    static QList<ComponentData*> getComponentsByDbId(const ComponentDataRoot* root, QString id);
    static ComponentData *_getChildComponent(ComponentData *data, QString id);

    static QList<ComponentData*> getComponentsByClass(const ComponentDataRoot *root, ComponentClass cls);
    static ComponentData* getHome(ComponentData* subRoot);

private:
    friend class Generator;
    friend class GeneratorWithModel;
    friend class GeneratorPages;

    //tree data
    bool m_childrenBuilt;
    ComponentData *m_parent;
    QList<ComponentData*> m_children;
    mutable QReadWriteLock m_childrenLock;

    //data
    Generator::IdSeparator m_idSeparator;
    IndexedString m_dbIdPrefix;
    QString m_childId;
    ComponentClass m_componentClass;
    Generator *m_generator;
    QString m_filename;
    QString m_name;
    QSet<IndexedString> m_fetchedRowData;
    //TODO QString m_rel;
    //TODO bool m_visible;
    //TODO bool m_inherits;
    //TODO QHash<IndexedString> m_tags;

    //static data
    static QList<ComponentData*> m_homes;

    //performance hashes
    QHash<int, ComponentData*> m_childIdsHash;

    //static performance hashes
    static QMultiHash<IndexedString, ComponentData*> m_dbIdHash;
    static QHash<ComponentClass, QList<ComponentData*> > m_componentClassHash;
    static QSet<ComponentClass> m_componentsByClassRequested;
protected:
    static QHash<const ComponentDataRoot*, QHash<QString, ComponentData*> > m_idHash;
};

QByteArray serialize(ComponentData *d);

#endif
