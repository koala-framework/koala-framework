#ifndef SELECTEXPR_H
#define SELECTEXPR_H

#include "IndexedString.h"
#include "Generator.h"

class Unserializer;
class ComponentData;

class SelectExpr {
public:
    virtual bool match(ComponentData*, ComponentData *parentData) const = 0;
    virtual bool mightMatch(const ComponentClass& cls) {
        Q_UNUSED(cls);
        return true; //kann wenn n?¢ùtig false zur??ckgeben f??r performance
                     //true ist aber _immer_ eine g??ltige antwort
    }
    virtual QByteArray serialize() const = 0;

    static SelectExpr *create(Unserializer *unserializer);
};
QDebug operator<<(QDebug dbg, const SelectExpr &s);
QByteArray serialize(const SelectExpr *e);


class SelectExprNot : public SelectExpr {
public:
    SelectExprNot(Unserializer *unserializer);
    SelectExprNot(SelectExpr *e) : m_expr(e) {}
    ~SelectExprNot() { delete m_expr; }
    virtual bool match(ComponentData *d, ComponentData *parentData) const {
        return !m_expr->match(d, parentData);
    }
    virtual bool mightMatch(const ComponentClass& cls) {
        Q_UNUSED(cls);
        return true;

        //das stimmt so nicht, da es ja _might_Match heisst und so ned umgedreht werden kann
        //return !m_expr->mightMatch(cls);
    }
    virtual QByteArray serialize() const;
    friend QDebug operator<<(QDebug, const SelectExprNot &);
private:
    SelectExpr *m_expr;
};
QDebug operator<<(QDebug dbg, const SelectExprNot &s);


class SelectExprWhereComponentType : public SelectExpr {
public:
    SelectExprWhereComponentType(Unserializer *unserializer, Generator::ComponentTypes type);
    SelectExprWhereComponentType(Generator::ComponentTypes type) : m_type(type) {}
    virtual bool match(ComponentData *d, ComponentData *parentData) const;
    friend QDebug operator<<(QDebug, const SelectExprWhereComponentType &);

private:
    Generator::ComponentTypes m_type;
};
QDebug operator<<(QDebug dbg, const SelectExprWhereComponentType &s);


class SelectExprWhereIsPage : public SelectExprWhereComponentType {
public:
    SelectExprWhereIsPage(Unserializer *unserializer)
      : SelectExprWhereComponentType(unserializer, Generator::TypePage) {}
    virtual QByteArray serialize() const;
};

class SelectExprWhereIsPseudoPage : public SelectExprWhereComponentType {
public:
    SelectExprWhereIsPseudoPage(Unserializer *unserializer)
      : SelectExprWhereComponentType(unserializer, Generator::TypePseudoPage) {}
    SelectExprWhereIsPseudoPage()
      : SelectExprWhereComponentType(Generator::TypePseudoPage) {}
    virtual QByteArray serialize() const;
};

class SelectExprWhereIsBox : public SelectExprWhereComponentType {
public:
    SelectExprWhereIsBox(Unserializer *unserializer)
      : SelectExprWhereComponentType(unserializer, Generator::TypeBox) {}
    virtual QByteArray serialize() const;
};

class SelectExprWhereIsMultiBox : public SelectExprWhereComponentType {
public:
    SelectExprWhereIsMultiBox(Unserializer *unserializer)
      : SelectExprWhereComponentType(unserializer, Generator::TypeMultiBox) {}
    virtual QByteArray serialize() const;
};

class SelectExprWhereShowInMenu : public SelectExprWhereComponentType {
public:
    SelectExprWhereShowInMenu(Unserializer *unserializer)
      : SelectExprWhereComponentType(unserializer, Generator::TypeShowInMenu) {}
    virtual QByteArray serialize() const;
    virtual bool match(ComponentData* d, ComponentData *parentData) const;
};

class SelectExprWhereInherit : public SelectExprWhereComponentType {
public:
    SelectExprWhereInherit(Unserializer *unserializer)
      : SelectExprWhereComponentType(unserializer, Generator::TypeInherit) {}
    virtual QByteArray serialize() const;
};

class SelectExprWhereUnique : public SelectExprWhereComponentType {
public:
    SelectExprWhereUnique(Unserializer *unserializer)
      : SelectExprWhereComponentType(unserializer, Generator::TypeUnique) {}
    virtual QByteArray serialize() const;
};

class SelectExprWhereHasFlag : public SelectExpr {
public:
    SelectExprWhereHasFlag(Unserializer *unserializer);
    SelectExprWhereHasFlag(IndexedString flag) : m_flag(flag) {}
    virtual bool match(ComponentData *d, ComponentData *parentData) const;
    virtual bool mightMatch(const ComponentClass& cls);
    virtual QByteArray serialize() const;
    friend QDebug operator<<(QDebug, const SelectExprWhereHasFlag &);

private:
    IndexedString m_flag;
};
QDebug operator<<(QDebug dbg, const SelectExprWhereHasFlag &s);


class SelectExprWhereFilename : public SelectExpr {
public:
    SelectExprWhereFilename(Unserializer *unserializer);
    SelectExprWhereFilename(const QString fn) : SelectExpr(), m_filename(fn) {}
    virtual bool match(ComponentData *d, ComponentData *parentData) const;
    virtual QByteArray serialize() const;
    friend QDebug operator<<(QDebug, const SelectExprWhereFilename &);

private:
    QString m_filename;
};
QDebug operator<<(QDebug dbg, const SelectExprWhereFilename &s);

class SelectExprWhereIsHome : public SelectExpr {
public:
    SelectExprWhereIsHome();
    SelectExprWhereIsHome(Unserializer *unserializer);
    virtual bool match(ComponentData *d, ComponentData *parentData) const;
    virtual QByteArray serialize() const;
};
QDebug operator<<(QDebug dbg, const SelectExprWhereIsHome &s);

class SelectExprWhereGenerator : public SelectExpr {
public:
    SelectExprWhereGenerator(Unserializer *unserializer);
    virtual bool match(ComponentData *d, ComponentData *parentData) const;
    virtual QByteArray serialize() const;
    friend QDebug operator<<(QDebug, const SelectExprWhereGenerator &);

    IndexedString generator() const {
        return m_generator;
    }

private:
    IndexedString m_generator;
};
QDebug operator<<(QDebug dbg, const SelectExprWhereGenerator &s);

class SelectExprWhereGeneratorClass : public SelectExpr {
public:
    SelectExprWhereGeneratorClass(Unserializer *unserializer);
    virtual bool match(ComponentData *d, ComponentData *parentData) const;
    virtual QByteArray serialize() const;
    friend QDebug operator<<(QDebug, const SelectExprWhereGeneratorClass &);

private:
    IndexedString m_generatorClass;
};
QDebug operator<<(QDebug dbg, const SelectExprWhereGeneratorClass &s);

class SelectExprWhereIdEquals : public SelectExpr {
public:
    SelectExprWhereIdEquals(Unserializer *unserializer);
    virtual bool match(ComponentData *d, ComponentData *parentData) const;
    virtual QByteArray serialize() const;
    friend QDebug operator<<(QDebug, const SelectExprWhereIdEquals &);

private:
    QString m_id;
};
QDebug operator<<(QDebug dbg, const SelectExprWhereIdEquals &s);


class SelectExprWherePageGenerator : public SelectExpr {
public:
    SelectExprWherePageGenerator(Unserializer *unserializer);
    virtual bool match(ComponentData *d, ComponentData *parentData) const;
    virtual QByteArray serialize() const;
    friend QDebug operator<<(QDebug, const SelectExprWherePageGenerator &);
};
QDebug operator<<(QDebug dbg, const SelectExprWherePageGenerator &s);

class SelectExprWhereSql : public SelectExpr {
public:
    SelectExprWhereSql(Unserializer *unserializer);
    virtual bool match(ComponentData *d, ComponentData *parentData) const;
    virtual QByteArray serialize() const;
    friend QDebug operator<<(QDebug, const SelectExprWhereSql &);
private:
    RawData m_cond;
    RawData m_value;
    RawData m_type;
};
QDebug operator<<(QDebug dbg, const SelectExprWhereSql &s);

class SelectExprWhereEquals : public SelectExpr {
public:
    SelectExprWhereEquals(Unserializer *unserializer);
    virtual bool match(ComponentData *d, ComponentData *parentData) const;
    virtual QByteArray serialize() const;
    friend QDebug operator<<(QDebug, const SelectExprWhereEquals &);
private:
    IndexedString m_field;
    QVariant m_value;
};
QDebug operator<<(QDebug dbg, const SelectExprWhereEquals &s);

class SelectExprWhereSubRoot : public SelectExpr {
public:
    SelectExprWhereSubRoot(Unserializer *unserializer);
    virtual bool match(ComponentData *d, ComponentData *parentData) const;
    virtual QByteArray serialize() const;
    friend QDebug operator<<(QDebug, const SelectExprWhereSubRoot &);
private:
    QString m_componentId;
};
QDebug operator<<(QDebug dbg, const SelectExprWhereSubRoot &s);


class SelectExprWhereVisible : public SelectExpr {
public:
    SelectExprWhereVisible(Unserializer *unserializer);
    virtual bool match(ComponentData *d, ComponentData *parentData) const;
    virtual QByteArray serialize() const;
    friend QDebug operator<<(QDebug, const SelectExprWhereVisible &);
};
QDebug operator<<(QDebug dbg, const SelectExprWhereVisible &s);


class SelectExprWhereHasEditComponents : public SelectExpr {
public:
    SelectExprWhereHasEditComponents(Unserializer *unserializer);
    virtual bool match(ComponentData *d, ComponentData *parentData) const;
    virtual bool mightMatch(const ComponentClass& cls);
    virtual QByteArray serialize() const;
    friend QDebug operator<<(QDebug, const SelectExprWhereHasEditComponents &);
};
QDebug operator<<(QDebug dbg, const SelectExprWhereHasEditComponents &s);

class SelectExprWhereComponentClasses : public SelectExpr {
public:
    SelectExprWhereComponentClasses(Unserializer *unserializer);
    virtual bool match(ComponentData *d, ComponentData *parentData) const;
    virtual QByteArray serialize() const;
    friend QDebug operator<<(QDebug, const SelectExprWhereComponentClasses &);
private:
    QList<ComponentClass> m_componentClasses;
};
QDebug operator<<(QDebug dbg, const SelectExprWhereComponentClasses &s);

/*
WhereComponentKey,
WhereComponentClasses,
WhereOnSamePage,
*/


#endif // SELECTEXPR_H
