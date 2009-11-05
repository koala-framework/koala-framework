#ifndef GENERATOR_H
#define GENERATOR_H

#include <QSqlQuery>

#include "ComponentClass.h"

class Select;
class ComponentData;

struct BuildStrategy
{
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
};

struct BuildWithDbIdShortcutStrategy : public BuildStrategy
{
    virtual bool skip(ComponentData *parent) const;
};

struct Generator
{
    Generator()
        : componentTypes(TypeComponent)
    {
        generators << this;
    }

    virtual ~Generator() {}

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

    enum ComponentType {
        TypeComponent  = 0x0000,
        TypePage       = 0x0001,
        TypePseudoPage = 0x0002,
        TypeBox        = 0x0004,
        TypeMultiBox   = 0x0008,
        TypeInherit    = 0x0010, //ob die komponente vererbt werden soll
        TypeUnique     = 0x0020,
        TypeInherits   = 0x0040, //ob die komponente andere erben soll
        TypeShowInMenu = 0x0080,
        TypePagesGenerator = 0x0100, //obs der pages-generator ist der die pages-tabelle ausliest
    };
    Q_DECLARE_FLAGS(ComponentTypes, ComponentType)

    ComponentClass componentClass;
    IndexedString key;
    IndexedString generatorClass;
    QList<IndexedString> parentClasses;
    IdSeparator idSeparator;
    IndexedString dbIdPrefix;
    ComponentTypes componentTypes; //TODO umbenennen, da inherit und unique auch dabei ist
    IndexedString model;
    IndexedString box; //TODO macht nicht in jedem generator sinn
    int priority; //TODO macht nicht in jedem generator sinn
    QList<ComponentData*> builtComponents;

    virtual bool showInMenu(ComponentData *d); //TODO should be const
    virtual bool isVisible(const ComponentData *d) const;

    virtual void build(ComponentData *parent) = 0;
    virtual void buildSingle(ComponentData *parent, const QString &id) = 0;
    virtual void refresh(ComponentData *d) = 0;
    virtual void preload() {}

    virtual QList<ComponentClass> childComponentClasses() = 0;
    virtual QList<IndexedString> childComponentKeys() = 0;

    static QHash<Type, int> buildCallCount;
    static QList<Generator*> generators;
    static QList<Generator*> inheritGenerators();

    static void buildWithGenerators(ComponentData* parent, const BuildStrategy *buildStrategy);

    enum ChangedRowMethod {
        RowUpdated,
        RowInserted,
        RowDeleted
    };
    static void handleChangedRow(ChangedRowMethod method, IndexedString model, const QString &id);
};
Q_DECLARE_OPERATORS_FOR_FLAGS(Generator::ComponentTypes)

struct GeneratorWithModel : public Generator
{
    void fetchRowData(ComponentData *parent, IndexedString field, const QString &onlyId = QString());
    QList<int> fetchIds(ComponentData *parent, const Select &select) const;

    virtual bool isVisible(const ComponentData* d) const;
};

struct GeneratorStatic : public Generator
{
    QHash<IndexedString, ComponentClass> component;
    QString filename;
    QString name;

    GeneratorStatic() : Generator()
    {
        Q_ASSERT(dbIdPrefix.isEmpty());
    }

    virtual void build(ComponentData *parent);
    virtual void buildSingle(ComponentData* parent, const QString& id);
    virtual void refresh(ComponentData* d);

    virtual QList<ComponentClass> childComponentClasses();
    virtual QList<IndexedString> childComponentKeys();
};

struct GeneratorTable : public GeneratorWithModel
{
    struct Row {
        Row(IndexedString id_, QString name_) : id(id_), name(name_) {}
        IndexedString id;
        QString name;
    };
    QList<Row> rows;
    ComponentClass component;
    virtual void build(ComponentData *parent);
    virtual void buildSingle(ComponentData* parent, const QString& id);
    virtual void refresh(ComponentData* d);

    virtual QList<ComponentClass> childComponentClasses();
    virtual QList<IndexedString> childComponentKeys();
};
struct GeneratorTableSql : public GeneratorWithModel
{
    QString tableName;
    bool whereComponentId;
    ComponentClass component;
    virtual void build(ComponentData *parent);
    virtual void buildSingle(ComponentData* parent, const QString& id);
    virtual void refresh(ComponentData* d);

    virtual QList<ComponentClass> childComponentClasses();
    virtual QList<IndexedString> childComponentKeys();

private:
    void _build(ComponentData* parent, QSqlQuery& query);
};
struct GeneratorTableSqlWithComponent : public GeneratorWithModel
{
    QString tableName;
    bool whereComponentId;
    QHash<IndexedString, ComponentClass> component;

    QHash<QString, QList<QPair<int, ComponentClass> > > data;

    virtual void preload();

    virtual void build(ComponentData *parent);
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
    ComponentClass component;
    virtual void build(ComponentData *parent);
    virtual void buildSingle(ComponentData* parent, const QString& id);
    virtual void refresh(ComponentData* d);

    virtual QList<ComponentClass> childComponentClasses();
    virtual QList<IndexedString> childComponentKeys();

private:
    void _build(ComponentData* parent, QSqlQuery query);
    QByteArray _sql(ComponentData* parent);
};
struct GeneratorLoadSqlWithComponent : public GeneratorWithModel
{
    QHash<IndexedString, ComponentClass> component;
    virtual void build(ComponentData *parent);
    virtual void buildSingle(ComponentData* parent, const QString& id);
    virtual void refresh(ComponentData* d);

    virtual QList<ComponentClass> childComponentClasses();
    virtual QList<IndexedString> childComponentKeys();

private:
    void _build(ComponentData* parent, QSqlQuery query);
    QByteArray _sql(ComponentData* parent);
};
struct GeneratorLoad : public GeneratorWithModel
{
    QHash<IndexedString, ComponentClass> component; //wird nur für childComponentClasses benötigt
    GeneratorLoad() : GeneratorWithModel()
    {
        Q_ASSERT(dbIdPrefix.isEmpty());
    }

    virtual void build(ComponentData* parent);
    virtual void buildSingle(ComponentData* parent, const QString& id);
    virtual void refresh(ComponentData* d);

    virtual QList<ComponentClass> childComponentClasses();
    virtual QList<IndexedString> childComponentKeys();

protected:
    QList<ComponentData*> _build(ComponentData* parent, QList<QByteArray> args);
};

struct GeneratorPages : public GeneratorLoad
{
    virtual void build(ComponentData* parent);
    virtual bool showInMenu(ComponentData* d);
};
struct GeneratorLinkTag : public Generator
{
    QHash<IndexedString, ComponentClass> component;
    QHash<QString, IndexedString> componentIdToComponent;

    GeneratorLinkTag() : Generator()
    {
        Q_ASSERT(dbIdPrefix.isEmpty());
    }

    virtual void preload();

    virtual void build(ComponentData* parent);
    virtual void buildSingle(ComponentData* parent, const QString& id);
    virtual void refresh(ComponentData* d);

    virtual QList<ComponentClass> childComponentClasses();
    virtual QList<IndexedString> childComponentKeys();
};

#endif // GENERATOR_H
