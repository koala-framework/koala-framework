#ifndef GENERATOR_H
#define GENERATOR_H

#include <QSqlQuery>

#include "ComponentClass.h"

struct ComponentDataRoot;
class Select;
class ComponentData;
class Generator;

struct BuildStrategy
{
    virtual ~BuildStrategy() {}
    virtual bool skip(ComponentData *parent) const = 0;
    virtual bool recurse() const { return true; }
};

struct BuildAllStrategy : public BuildStrategy
{
    virtual bool skip(ComponentData *parent) const {
        Q_UNUSED(parent);
        return false;
    }
};

struct BuildNoChildrenStrategy : public BuildAllStrategy
{
    virtual bool recurse() const { return false; }
};
/*
struct BuildOnlyComponentClassStrategy : public BuildStrategy
{
    BuildOnlyComponentClassStrategy(ComponentClass c) : cc(c) {}
    virtual bool skip(ComponentData *parent) const;

private:
    ComponentClass cc;
};
*/
struct BuildOnlyPagesGeneratorStrategy : public BuildStrategy
{
    virtual bool skip(ComponentData *parent) const;
private:
    friend class Generator;
    bool canHavePagesGeneratorAsChild(Generator *generator) const;
    static QHash<Generator *, bool> m_cache;
};

struct BuildWithDbIdShortcutStrategy : public BuildStrategy
{
    virtual bool skip(ComponentData *parent) const;
private:
    friend class Generator;
    enum DbIdShortcutType {
        NoDbIdShortcut,
        DirectDbIdShortcut,
        IndirectDbIdShortcut
    };
    DbIdShortcutType canHaveDbIdShortcutAsChild(Generator *generator) const;
    static QHash<Generator *, DbIdShortcutType> m_cache;
};

struct Generator
{
    Generator(const ComponentDataRoot *root)
        : generatorFlags(TypeComponent), uniqueFilename(false)
    {
        m_root = root;
        m_generators[root] << this;
    }

    virtual ~Generator();
    
    static void deleteGenerators(const ComponentDataRoot *root);

    enum IdSeparator {
        Dash,
        Underscore,
        NoSeparator //für root + seiten aus seitenbaum
    };

    enum Type {
        Unknown,
        Static,
        Table,
        TableSql,
        Load,
        Pages,
        TableSqlWithComponent,
        LoadSql,
        LoadSqlWithComponent,
        LinkTag
    };

    enum GeneratorFlag {
        TypeComponent      = 0x0000,
        TypePage           = 0x0001,
        TypePseudoPage     = 0x0002,
        TypeBox            = 0x0004,
        TypeMultiBox       = 0x0008,
        TypePagesGenerator = 0x0010, //obs der pages-generator ist der die pages-tabelle ausliest

        DisableCache       = 0x0020, //ob der komponenten baum cache deaktivert ist

        TypeInherit        = 0x0100, //ob die komponente vererbt werden soll
        TypeUnique         = 0x0200,
        TypeInherits       = 0x0400, //ob die komponente andere erben soll
        TypeShowInMenu     = 0x0800,
        
        ColumnComponentId  = 0x1000,
        ColumnComponent    = 0x2000,
        ColumnVisible      = 0x4000
    };
    Q_DECLARE_FLAGS(GeneratorFlags, GeneratorFlag)

    ComponentClass componentClass;
    IndexedString key;
    IndexedString generatorClass;
    QList<IndexedString> parentClasses;
    IdSeparator idSeparator;
    IndexedString dbIdPrefix;
    GeneratorFlags generatorFlags;
    IndexedString model;
    IndexedString box; //TODO macht nicht in jedem generator sinn
    int priority; //TODO macht nicht in jedem generator sinn
    bool uniqueFilename; //TODO macht nicht in jedem generator sinn
    QList<ComponentData*> builtComponents;

    const ComponentDataRoot *root() const {
        return m_root;
    }

    virtual bool showInMenu(ComponentData *d); //TODO should be const
    virtual bool isVisible(const ComponentData *d) const;
    virtual QList<QString> tags(const ComponentData *d) const;

    virtual QList<ComponentData*> build(ComponentData *parent) = 0;
    virtual QList<ComponentData*> buildDynamic(ComponentData *parent, const Select &select) { 
        Q_UNUSED(parent);
        Q_UNUSED(select);
        Q_ASSERT(0); 
        return QList<ComponentData*>();
    }
    virtual void buildSingle(ComponentData *parent, const QString &id) = 0;
    virtual void refresh(ComponentData *d) = 0;
    virtual void preload() {}

    virtual QList<ComponentClass> childComponentClasses() = 0;
    virtual QList<IndexedString> childComponentKeys() = 0;

    static QHash<Type, int> buildCallCount;
    static QList<Generator*> generators(const ComponentDataRoot* root) {
        return m_generators[root];
    }

    //generators von allen roots!
    static QList<Generator*> generators()
    {
        QList<Generator*> ret;
        foreach (QList<Generator*> l, m_generators.values()) {
            ret << l;
        }
        return ret;
    }

    static QList<Generator*> inheritGenerators(const ComponentDataRoot* root);

    static void buildWithGenerators(ComponentData* parent, const BuildStrategy *buildStrategy);

    enum ChangedRowMethod {
        RowUpdated,
        RowInserted,
        RowDeleted
    };
    static void handleChangedRow(ChangedRowMethod method, IndexedString model, const QString &id);

    static void createGenerators(const ComponentDataRoot* root);
private:
    static QHash<const ComponentDataRoot *, QList<Generator*> > m_generators;
    static QHash<const ComponentDataRoot*, QList<Generator*> > m_inheritGeneratorsCache;

    const ComponentDataRoot *m_root;
};
Q_DECLARE_OPERATORS_FOR_FLAGS(Generator::GeneratorFlags)

struct GeneratorWithModel : public Generator
{
    GeneratorWithModel(const ComponentDataRoot* root) : Generator(root) {}

    QHash<int, QVariant> rowData(IndexedString field, const QString& onlyId = QString()) const;
    void fetchRowData(ComponentData *parent, IndexedString field, const QString &onlyId = QString());
    QList<int> fetchIds(ComponentData *parent, const Select &select) const;

    virtual bool isVisible(const ComponentData* d) const;
};

struct GeneratorStatic : public Generator
{
    QHash<IndexedString, ComponentClass> component;
    QString filename;
    QString name;

    GeneratorStatic(const ComponentDataRoot *root) : Generator(root)
    {
        Q_ASSERT(dbIdPrefix.isEmpty());
    }

    virtual QList<ComponentData*> build(ComponentData *parent);
    virtual void buildSingle(ComponentData* parent, const QString& id);
    virtual void refresh(ComponentData* d);

    virtual QList<ComponentClass> childComponentClasses();
    virtual QList<IndexedString> childComponentKeys();
};

struct GeneratorTable : public GeneratorWithModel
{
    GeneratorTable(const ComponentDataRoot *root) : GeneratorWithModel(root) {}

    QHash<IndexedString, ComponentClass> component;
    
    virtual QList<ComponentData*> build(ComponentData *parent);
    virtual QList<ComponentData*> buildDynamic(ComponentData *parent, const Select &select);
    virtual void buildSingle(ComponentData* parent, const QString& id);
    virtual void refresh(ComponentData* d);

    virtual QList<ComponentClass> childComponentClasses();
    virtual QList<IndexedString> childComponentKeys();

private:
    QList<ComponentData*> _build(ComponentData* parent, const Select& select);
};

struct GeneratorTableSql : public GeneratorWithModel
{
    GeneratorTableSql(const ComponentDataRoot *root) : GeneratorWithModel(root) {}

    QString tableName;
    ComponentClass component;
    virtual QList<ComponentData*> build(ComponentData *parent);
    virtual void buildSingle(ComponentData* parent, const QString& id);
    virtual void refresh(ComponentData* d);

    virtual QList<ComponentClass> childComponentClasses();
    virtual QList<IndexedString> childComponentKeys();

private:
    QList<ComponentData*> _build(ComponentData* parent, QSqlQuery& query);
};
struct GeneratorTableSqlWithComponent : public GeneratorWithModel
{
    GeneratorTableSqlWithComponent(const ComponentDataRoot *root) : GeneratorWithModel(root) {}

    QString tableName;
    QHash<IndexedString, ComponentClass> component;

    QHash<QString, QList<QPair<int, ComponentClass> > > data;

    virtual void preload();

    virtual QList<ComponentData*> build(ComponentData *parent);
    virtual void buildSingle(ComponentData* parent, const QString& id);
    virtual void refresh(ComponentData* d);

    virtual QList<ComponentClass> childComponentClasses();
    virtual QList<IndexedString> childComponentKeys();

    //verwendet von ComponentData::getComponentsByClass
    //um einen einsprungspunkt für paragraphs zu haben
    QList<QString> fetchParentDbIds(ComponentClass cc);

private:
    QList< QPair< int, ComponentClass > > _items(ComponentData* parent);
};
struct GeneratorLoadSql : public GeneratorWithModel
{
    GeneratorLoadSql(const ComponentDataRoot *root) : GeneratorWithModel(root) {}

    ComponentClass component;
    virtual QList<ComponentData*> build(ComponentData *parent);
    virtual void buildSingle(ComponentData* parent, const QString& id);
    virtual void refresh(ComponentData* d);

    virtual QList<ComponentClass> childComponentClasses();
    virtual QList<IndexedString> childComponentKeys();

private:
    QList<ComponentData*> _build(ComponentData* parent, QSqlQuery query);
    QByteArray _sql(ComponentData* parent, int id = 0);
};
struct GeneratorLoadSqlWithComponent : public GeneratorWithModel
{
    GeneratorLoadSqlWithComponent(const ComponentDataRoot *root) : GeneratorWithModel(root) {}

    QHash<IndexedString, ComponentClass> component;
    virtual QList<ComponentData*> build(ComponentData *parent);
    virtual void buildSingle(ComponentData* parent, const QString& id);
    virtual void refresh(ComponentData* d);

    virtual QList<ComponentClass> childComponentClasses();
    virtual QList<IndexedString> childComponentKeys();

private:
    QList<ComponentData*> _build(ComponentData* parent, QSqlQuery query);
    QByteArray _sql(ComponentData* parent);
};
struct GeneratorLoad : public GeneratorWithModel
{
    QHash<IndexedString, ComponentClass> component; //wird nur für childComponentClasses benötigt
    GeneratorLoad(const ComponentDataRoot* root) : GeneratorWithModel(root)
    {
        Q_ASSERT(dbIdPrefix.isEmpty());
    }

    virtual QList<ComponentData*> build(ComponentData* parent);
    virtual QList<ComponentData*> buildDynamic(ComponentData *parent, const Select &select);
    virtual void buildSingle(ComponentData* parent, const QString& id);
    virtual void refresh(ComponentData* d);

    virtual QList<ComponentClass> childComponentClasses();
    virtual QList<IndexedString> childComponentKeys();

protected:
    QList<ComponentData*> _build(ComponentData* parent, QList<QByteArray> args);
};

struct GeneratorPages : public GeneratorLoad
{
    GeneratorPages(const ComponentDataRoot *root) : GeneratorLoad(root) {}

    virtual QList<ComponentData*> build(ComponentData* parent);
    virtual bool showInMenu(ComponentData* d);
    virtual QList<QString> tags(const ComponentData* d) const;
};
struct GeneratorLinkTag : public Generator
{
    QHash<IndexedString, ComponentClass> component;
    QHash<QString, IndexedString> componentIdToComponent;

    GeneratorLinkTag(const ComponentDataRoot *root) : Generator(root)
    {
        Q_ASSERT(dbIdPrefix.isEmpty());
    }

    virtual void preload();

    virtual QList<ComponentData*> build(ComponentData* parent);
    virtual void buildSingle(ComponentData* parent, const QString& id);
    virtual void refresh(ComponentData* d);

    virtual QList<ComponentClass> childComponentClasses();
    virtual QList<IndexedString> childComponentKeys();
};

#endif // GENERATOR_H
