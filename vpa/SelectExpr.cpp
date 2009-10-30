#include "SelectExpr.h"

#include "Unserializer.h"
#include "ComponentData.h"


SelectExpr* SelectExpr::create(Unserializer* unserializer)
{
    QByteArray className = unserializer->readObjectClassName();
    if (className == "Vps_Model_Select_Expr_Component_HasFlag") {
        return new SelectExprWhereHasFlag(unserializer);
    } else if (className == "Vps_Model_Select_Expr_Component_IsPage") {
        return new SelectExprWhereIsPage(unserializer);
    } else if (className == "Vps_Model_Select_Expr_Component_IsPseudoPage") {
        return new SelectExprWhereIsPseudoPage(unserializer);
    } else if (className == "Vps_Model_Select_Expr_Component_IsBox") {
        return new SelectExprWhereIsBox(unserializer);
    } else if (className == "Vps_Model_Select_Expr_Component_IsMultiBox") {
        return new SelectExprWhereIsMultiBox(unserializer);
    } else if (className == "Vps_Model_Select_Expr_Component_ShowInMenu") {
        return new SelectExprWhereShowInMenu(unserializer);
    } else if (className == "Vps_Model_Select_Expr_Not") {
        return new SelectExprNot(unserializer);
    } else if (className == "Vps_Model_Select_Expr_Component_Filename") {
        return new SelectExprWhereFilename(unserializer);
    } else if (className == "Vps_Model_Select_Expr_Component_IsHome") {
        return new SelectExprWhereIsHome(unserializer);
    } else if (className == "Vps_Model_Select_Expr_Component_Inherit") {
        return new SelectExprWhereInherit(unserializer);
    } else if (className == "Vps_Model_Select_Expr_Component_Unique") {
        return new SelectExprWhereUnique(unserializer);
    } else if (className == "Vps_Model_Select_Expr_Component_Generator") {
        return new SelectExprWhereGenerator(unserializer);
    } else if (className == "Vps_Model_Select_Expr_Component_GeneratorClass") {
        return new SelectExprWhereGeneratorClass(unserializer);
    } else if (className == "Vps_Model_Select_Expr_IdEquals") {
        return new SelectExprWhereIdEquals(unserializer);
    } else if (className == "Vps_Model_Select_Expr_Component_PageGenerator") {
        return new SelectExprWherePageGenerator(unserializer);
    } else if (className == "Vps_Model_Select_Expr_WhereSql") {
        return new SelectExprWhereSql(unserializer);
    } else if (className == "Vps_Model_Select_Expr_Equals") {
        return new SelectExprWhereEquals(unserializer);
    } else if (className == "Vps_Model_Select_Expr_Component_SubRoot") {
        return new SelectExprWhereSubRoot(unserializer);
    } else if (className == "Vps_Model_Select_Expr_Component_Visible") {
        return new SelectExprWhereVisible(unserializer);
    } else if (className == "Vps_Model_Select_Expr_Component_HasEditComponents") {
        return new SelectExprWhereHasEditComponents(unserializer);
    }
    qWarning() << className;
    Q_ASSERT(0);
    return 0;
}

QDebug operator<<(QDebug dbg, const SelectExpr& se)
{
    const SelectExpr *e = &se;
    if (dynamic_cast<const SelectExprWhereHasFlag*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereHasFlag*>(e);
    } else if (dynamic_cast<const SelectExprWhereIsPage*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereIsPage*>(e);
    } else if (dynamic_cast<const SelectExprWhereIsPseudoPage*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereIsPseudoPage*>(e);
    } else if (dynamic_cast<const SelectExprWhereIsBox*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereIsBox*>(e);
    } else if (dynamic_cast<const SelectExprWhereIsMultiBox*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereIsMultiBox*>(e);
    } else if (dynamic_cast<const SelectExprWhereShowInMenu*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereShowInMenu*>(e);
    } else if (dynamic_cast<const SelectExprNot*>(e)) {
        dbg.space() << *static_cast<const SelectExprNot*>(e);
    } else if (dynamic_cast<const SelectExprWhereFilename*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereFilename*>(e);
    } else if (dynamic_cast<const SelectExprWhereIsHome*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereIsHome*>(e);
    } else if (dynamic_cast<const SelectExprWhereInherit*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereInherit*>(e);
    } else if (dynamic_cast<const SelectExprWhereUnique*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereUnique*>(e);
    } else if (dynamic_cast<const SelectExprWhereGenerator*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereGenerator*>(e);
    } else if (dynamic_cast<const SelectExprWhereGeneratorClass*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereGeneratorClass*>(e);
    } else if (dynamic_cast<const SelectExprWhereIdEquals*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereIdEquals*>(e);
    } else if (dynamic_cast<const SelectExprWherePageGenerator*>(e)) {
        dbg.space() << *static_cast<const SelectExprWherePageGenerator*>(e);
    } else if (dynamic_cast<const SelectExprWhereSql*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereSql*>(e);
    } else if (dynamic_cast<const SelectExprWhereEquals*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereEquals*>(e);
    } else if (dynamic_cast<const SelectExprWhereSubRoot*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereSubRoot*>(e);
    } else if (dynamic_cast<const SelectExprWhereVisible*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereVisible*>(e);
    } else if (dynamic_cast<const SelectExprWhereHasEditComponents*>(e)) {
        dbg.space() << *static_cast<const SelectExprWhereHasEditComponents*>(e);
    } else {
        dbg.space() << "unknown expression";
    }
    return dbg.nospace();
}

QByteArray serialize(const SelectExpr* e)
{
    return e->serialize();
}

SelectExprNot::SelectExprNot(Unserializer* unserializer)
{
    int numProperties = unserializer->readNumber();
    QByteArray in = unserializer->device()->read(2);
    Q_ASSERT(in == ":{");
    for (int i=0; i < numProperties; ++i) {
        QByteArray varName = unserializer->readString();
        varName = varName.replace('\0', "");
        if (varName == "Vps_Model_Select_Expr_Not_expression") {
            m_expr = SelectExpr::create(unserializer);
        }
    }
    in = unserializer->device()->read(1);
    Q_ASSERT(in == "}");
}

QDebug operator<<(QDebug dbg, const SelectExprNot& s)
{
    dbg.nospace() << "!(" << *s.m_expr << ")";
    return dbg.space();
}

QByteArray SelectExprNot::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Not");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":1:{";
    ret += serializePrivateObjectProperty("_expression", "Vps_Model_Select_Expr_Not", m_expr);
    ret += "}";
    return ret;
}

SelectExprWhereComponentType::SelectExprWhereComponentType(Unserializer* unserializer, Generator::ComponentTypes type)
    : m_type(type)
{
    int numProperties = unserializer->readNumber();
    Q_ASSERT(numProperties== 0);
    QByteArray in = unserializer->device()->read(3);
    Q_ASSERT(in == ":{}");
}


bool SelectExprWhereComponentType::match(ComponentData* d) const
{
    return d->componentTypes() & m_type;
}

QDebug operator<<(QDebug dbg, const SelectExprWhereComponentType& s)
{
    dbg.nospace() << "SelectExprWhereComponentType(";
    if (s.m_type & Generator::TypePage) dbg << "Page ";
    if (s.m_type & Generator::TypePseudoPage) dbg << "PseudoPage ";
    if (s.m_type & Generator::TypeBox) dbg << "Box ";
    if (s.m_type & Generator::TypeMultiBox) dbg << "MultiBox ";
    if (s.m_type & Generator::TypeInherit) dbg << "Inherit ";
    if (s.m_type & Generator::TypeUnique) dbg << "Unique ";
    if (s.m_type & Generator::TypeInherits) dbg << "Inherits ";
    if (s.m_type & Generator::TypeShowInMenu) dbg << "ShowInMenu ";
    dbg << ")";
    return dbg.space();
}

QByteArray SelectExprWhereIsPage::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Component_IsPage");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":0:{";
    ret += "}";
    return ret;
}

QByteArray SelectExprWhereInherit::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Component_Inherit");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":0:{";
    ret += "}";
    return ret;
}

QByteArray SelectExprWhereIsBox::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Component_IsBox");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":0:{";
    ret += "}";
    return ret;
}

QByteArray SelectExprWhereIsMultiBox::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Component_IsMultiBox");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":0:{";
    ret += "}";
    return ret;
}

QByteArray SelectExprWhereIsPseudoPage::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Component_IsPseudoPage");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":0:{";
    ret += "}";
    return ret;
}

QByteArray SelectExprWhereShowInMenu::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Component_ShowInMenu");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":0:{";
    ret += "}";
    return ret;
}

bool SelectExprWhereShowInMenu::match(ComponentData* d) const
{
    return d->generator()->showInMenu(d);
}


QByteArray SelectExprWhereUnique::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Component_Unique");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":0:{";
    ret += "}";
    return ret;
}

SelectExprWhereHasFlag::SelectExprWhereHasFlag(Unserializer* unserializer)
{
    int numProperties = unserializer->readNumber();
    QByteArray in = unserializer->device()->read(2);
    Q_ASSERT(in == ":{");
    for (int i=0; i < numProperties; ++i) {
        QByteArray varName = unserializer->readString();
        varName = varName.replace('\0', "");
        if (varName == "Vps_Model_Select_Expr_Component_HasFlag_flag") {
            m_flag = IndexedString(unserializer->readString());
        }
    }
    in = unserializer->device()->read(1);
    Q_ASSERT(in == "}");
}

bool SelectExprWhereHasFlag::match(ComponentData* d) const
{
    if (!d->hasFlag(m_flag)) return false;
    return true;
}

QByteArray SelectExprWhereHasFlag::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Component_Unique");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":1:{";
    ret += serializePrivateObjectProperty("_flag", "Vps_Model_Select_Expr_Component_HasFlag", m_flag);
    ret += "}";
    return ret;
}

QDebug operator<<(QDebug dbg, const SelectExprWhereHasFlag& s)
{
    dbg.nospace() << "SelectExprWhereHasFlag" << s.m_flag;
    return dbg.nospace();
}

SelectExprWhereFilename::SelectExprWhereFilename(Unserializer* unserializer)
{
    int numProperties = unserializer->readNumber();
    QByteArray in = unserializer->device()->read(2);
    Q_ASSERT(in == ":{");
    for (int i=0; i < numProperties; ++i) {
        QByteArray varName = unserializer->readString();
        varName = varName.replace('\0', "");
        if (varName == "Vps_Model_Select_Expr_Component_Filename_filename") {
            m_filename = QString::fromUtf8(unserializer->readString());
        }
    }
    in = unserializer->device()->read(1);
    Q_ASSERT(in == "}");
}

bool SelectExprWhereFilename::match(ComponentData* d) const
{
    return d->filename() == m_filename;
}

QByteArray SelectExprWhereFilename::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Component_Filename");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":1:{";
    ret += serializePrivateObjectProperty("_filename", "Vps_Model_Select_Expr_Component_Filename", m_filename);
    ret += "}";
    return ret;
}

QDebug operator<<(QDebug dbg, const SelectExprWhereFilename& s)
{
    dbg.nospace() << "SelectExprWhereFilename" << s.m_filename;
    return dbg.nospace();
}

SelectExprWhereIsHome::SelectExprWhereIsHome()
{
}

SelectExprWhereIsHome::SelectExprWhereIsHome(Unserializer* unserializer)
{
    int numProperties = unserializer->readNumber();
    Q_ASSERT(numProperties== 0);
    QByteArray in = unserializer->device()->read(3);
    Q_ASSERT(in == ":{}");
}

bool SelectExprWhereIsHome::match(ComponentData* d) const
{
    return d->isHome();
}

QByteArray SelectExprWhereIsHome::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Component_IsHome");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":0:{";
    ret += "}";
    return ret;
}

QDebug operator<<(QDebug dbg, const SelectExprWhereIsHome&)
{
    dbg.nospace() << "SelectExprWhereIsHome";
    return dbg.nospace();
}

SelectExprWhereGenerator::SelectExprWhereGenerator(Unserializer* unserializer)
{
    int numProperties = unserializer->readNumber();
    QByteArray in = unserializer->device()->read(2);
    Q_ASSERT(in == ":{");
    for (int i=0; i < numProperties; ++i) {
        QByteArray varName = unserializer->readString();
        varName = varName.replace('\0', "");
        if (varName == "Vps_Model_Select_Expr_Component_Generator_generator") {
            m_generator = IndexedString(unserializer->readString());
        }
    }
    in = unserializer->device()->read(1);
    Q_ASSERT(in == "}");
}

bool SelectExprWhereGenerator::match(ComponentData* d) const
{
    return d->generator()->key == m_generator;
}


QByteArray SelectExprWhereGenerator::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Component_Generator");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":1:{";
    ret += serializePrivateObjectProperty("_generator", "Vps_Model_Select_Expr_Component_Generator", m_generator);
    ret += "}";
    return ret;
}

QDebug operator<<(QDebug dbg, const SelectExprWhereGenerator& s)
{
    dbg.nospace() << "SelectExprWhereGenerator" << s.m_generator;
    return dbg.nospace();
}

SelectExprWhereGeneratorClass::SelectExprWhereGeneratorClass(Unserializer* unserializer)
{
    int numProperties = unserializer->readNumber();
    QByteArray in = unserializer->device()->read(2);
    Q_ASSERT(in == ":{");
    for (int i=0; i < numProperties; ++i) {
        QByteArray varName = unserializer->readString();
        varName = varName.replace('\0', "");
        if (varName == "Vps_Model_Select_Expr_Component_GeneratorClass_generatorClass") {
            m_generatorClass = IndexedString(unserializer->readString());
        }
    }
    in = unserializer->device()->read(1);
    Q_ASSERT(in == "}");
}

bool SelectExprWhereGeneratorClass::match(ComponentData* d) const
{
    if (d->generator()->generatorClass == m_generatorClass) {
        return true;
    }
    foreach (const IndexedString &c, d->generator()->parentClasses) {
        if (c == m_generatorClass) {
            return true;
        }
    }
    return false;
}

QByteArray SelectExprWhereGeneratorClass::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Component_GeneratorClass");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":1:{";
    ret += serializePrivateObjectProperty("_generatorClass", "Vps_Model_Select_Expr_Component_GeneratorClass", m_generatorClass);
    ret += "}";
    return ret;
}

QDebug operator<<(QDebug dbg, const SelectExprWhereGeneratorClass& s)
{
    dbg.nospace() << "SelectExprWhereGeneratorClass" << s.m_generatorClass;
    return dbg.nospace();
}

SelectExprWhereIdEquals::SelectExprWhereIdEquals(Unserializer* unserializer)
{
    int numProperties = unserializer->readNumber();
    QByteArray in = unserializer->device()->read(2);
    Q_ASSERT(in == ":{");
    for (int i=0; i < numProperties; ++i) {
        QByteArray varName = unserializer->readString();
        varName = varName.replace('\0', "");
        if (varName == "Vps_Model_Select_Expr_IdEquals_id") {
            m_id = QString::fromUtf8(unserializer->readString());
        }
    }
    in = unserializer->device()->read(1);
    Q_ASSERT(in == "}");
}

bool SelectExprWhereIdEquals::match(ComponentData* d) const
{
    return d->childIdWithSeparator() == m_id;
}

QByteArray SelectExprWhereIdEquals::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_IdEquals");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":1:{";
    ret += serializePrivateObjectProperty("_id", "Vps_Model_Select_Expr_IdEquals", m_id);
    ret += "}";
    return ret;
}

QDebug operator<<(QDebug dbg, const SelectExprWhereIdEquals& s)
{
    dbg.nospace() << "SelectExprWhereIdEquals" << s.m_id;
    return dbg.nospace();
}


SelectExprWherePageGenerator::SelectExprWherePageGenerator(Unserializer* unserializer)
{
    int numProperties = unserializer->readNumber();
    Q_ASSERT(numProperties== 0);
    QByteArray in = unserializer->device()->read(3);
    Q_ASSERT(in == ":{}");
}

bool SelectExprWherePageGenerator::match(ComponentData* d) const
{
    //TODO: kann diese klasse im web abgeleitet werden?
    static IndexedString c("Vps_Component_Generator_Page");
    static IndexedString c2("Vpc_Root_DomainRoot_Category_PageGenerator");
    return d->generator()->generatorClass == c || d->generator()->generatorClass == c2;
}

QByteArray SelectExprWherePageGenerator::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Component_PageGenerator");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":0:{";
    ret += "}";
    return ret;
}

QDebug operator<<(QDebug dbg, const SelectExprWherePageGenerator&)
{
    dbg.nospace() << "SelectExprWherePageGenerator";
    return dbg.nospace();
}

SelectExprWhereSql::SelectExprWhereSql(Unserializer* unserializer)
{
    int numProperties = unserializer->readNumber();
    QByteArray in = unserializer->device()->read(2);
    Q_ASSERT(in == ":{");
    for (int i=0; i < numProperties; ++i) {
        QByteArray varName = unserializer->readString();
        varName = varName.replace('\0', "");
        if (varName == "Vps_Model_Select_Expr_WhereSql_cond") {
            m_cond = unserializer->readRawData();
        } else if (varName == "Vps_Model_Select_Expr_WhereSql_value") {
            m_value = unserializer->readRawData();
        } else if (varName == "Vps_Model_Select_Expr_WhereSql_type") {
            m_type = unserializer->readRawData();
        }
    }
    in = unserializer->device()->read(1);
    Q_ASSERT(in == "}");
}

bool SelectExprWhereSql::match(ComponentData* d) const
{
    Q_UNUSED(d);
    //not implemented, da müsste direkt die datenbank gefragt werden oder so
    return true;
}

QByteArray SelectExprWhereSql::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_WhereSql");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":3:{";
    ret += serializePrivateObjectProperty("_cond", "Vps_Model_Select_Expr_WhereSql", m_cond);
    ret += serializePrivateObjectProperty("_value", "Vps_Model_Select_Expr_WhereSql", m_value);
    ret += serializePrivateObjectProperty("_type", "Vps_Model_Select_Expr_WhereSql", m_type);
    ret += "}";
    return ret;
}

QDebug operator<<(QDebug dbg, const SelectExprWhereSql& s)
{
    dbg.nospace() << "SelectExprWhereSql(" << s.m_cond << s.m_value << s.m_type << ")";
    return dbg.nospace();
}


SelectExprWhereEquals::SelectExprWhereEquals(Unserializer* unserializer)
{
    int numProperties = unserializer->readNumber();
    QByteArray in = unserializer->device()->read(2);
    Q_ASSERT(in == ":{");
    for (int i=0; i < numProperties; ++i) {
        QByteArray varName = unserializer->readString();
        varName = varName.replace('\0', "");
        if (varName == "*_field") {
            m_field = IndexedString(unserializer->readString());
        } else if (varName == "*_value") {
            m_value = unserializer->readVariant();
        }
    }
    in = unserializer->device()->read(1);
    if (in != "}") {
        qDebug() << in;
    }
    Q_ASSERT(in == "}");
}

bool SelectExprWhereEquals::match(ComponentData* d) const
{
    if (!dynamic_cast<GeneratorWithModel*>(d->generator())) return false;
    static_cast<GeneratorWithModel*>(d->generator())->fetchRowData(d->parent(), m_field);
    if (!d->rowData.contains(m_field)) return false;
    if (d->rowData[m_field] != m_value) {
//         qDebug() << d->rowData[m_field] << "!=" << m_value;
        return false;
    }
    return true;
}

QByteArray SelectExprWhereEquals::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Equals");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":2:{";
    ret += serializePrivateObjectProperty("_field", "Vps_Model_Select_Expr_Equals", m_field);
    ret += serializePrivateObjectProperty("_value", "Vps_Model_Select_Expr_Equals", m_value);
    ret += "}";
    return ret;
}

QDebug operator<<(QDebug dbg, const SelectExprWhereEquals& s)
{
    dbg.nospace() << "SelectExprWhereEquals(" << s.m_field <<"=" << s.m_value << ")";
    return dbg.nospace();
}

SelectExprWhereSubRoot::SelectExprWhereSubRoot(Unserializer* unserializer)
{
    int numProperties = unserializer->readNumber();
    QByteArray in = unserializer->device()->read(2);
    Q_ASSERT(in == ":{");
    for (int i=0; i < numProperties; ++i) {
        QByteArray varName = unserializer->readString();
        varName = varName.replace('\0', "");
        if (varName == "Vps_Model_Select_Expr_Component_SubRoot_componentId") {
            m_componentId = QString::fromUtf8(unserializer->readString());
        }
    }
    in = unserializer->device()->read(1);
    Q_ASSERT(in == "}");
}

QByteArray SelectExprWhereSubRoot::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Component_SubRoot");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":1:{";
    ret += serializePrivateObjectProperty("_componentId", "Vps_Model_Select_Expr_Component_SubRoot", m_componentId);
    ret += "}";
    return ret;
}

bool SelectExprWhereSubRoot::match(ComponentData* d) const
{
    ComponentData *sr = ComponentData::getComponentById(m_componentId);
    while (!sr->hasFlag(IndexedString("subroot"))) {
        sr = sr->parent();
        if (!sr) return false;
    }
    do {
        if (d == sr) return true;
    } while ((d = d->parent()));
    return false;
}

QDebug operator<<(QDebug dbg, const SelectExprWhereSubRoot& s)
{
    dbg.nospace() << "SelectExprWhereSubRoot(" << s.m_componentId << ")";
    return dbg.nospace();
}

SelectExprWhereVisible::SelectExprWhereVisible(Unserializer* unserializer)
{
    int numProperties = unserializer->readNumber();
    QByteArray in = unserializer->device()->read(2);
    Q_ASSERT(in == ":{");
    for (int i=0; i < numProperties; ++i) {
        QByteArray varName = unserializer->readString();
        varName = varName.replace('\0', "");
    }
    in = unserializer->device()->read(1);
    Q_ASSERT(in == "}");
}

QByteArray SelectExprWhereVisible::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Component_Visible");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":0:{";
    ret += "}";
    return ret;
}

bool SelectExprWhereVisible::match(ComponentData* d) const
{
    return true; //TODO: performanceproblem, erstmal deaktiviert
    /*
    if (!d->isVisible()) return false;
    while ((d = d->parent())) {
        if (!d->parent()->isVisible()) return false;
    }
    return true;
    */
}

QDebug operator<<(QDebug dbg, const SelectExprWhereVisible& s)
{
    dbg.nospace() << "SelectExprWhereVisible";
    return dbg.space();
}



SelectExprWhereHasEditComponents::SelectExprWhereHasEditComponents(Unserializer* unserializer)
{
    int numProperties = unserializer->readNumber();
    Q_ASSERT(numProperties== 0);
    QByteArray in = unserializer->device()->read(3);
    Q_ASSERT(in == ":{}");
}

bool SelectExprWhereHasEditComponents::match(ComponentData* d) const
{
    QList<IndexedString> ec = d->generator()->componentClass.editComponents();
    foreach (const IndexedString &i, ec) {
        if (d->generator()->childComponentKeys().contains(i)) {
            return true;
        }
    }
    return false;
}

QByteArray SelectExprWhereHasEditComponents::serialize() const
{
    QByteArray ret;
    QByteArray cls("Vps_Model_Select_Expr_Component_HasEditComponents");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":0:{";
    ret += "}";
    return ret;
}

QDebug operator<<(QDebug dbg, const SelectExprWhereHasEditComponents&)
{
    dbg.nospace() << "SelectExprWhereHasEditComponents";
    return dbg.nospace();
}









