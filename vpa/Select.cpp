#include "Select.h"

#include "Unserializer.h"
#include "ComponentData.h"


Select::Select()
    : limitCount(0), limitOffset(0)
{
}


Select::Select(Unserializer* unserializer)
    : limitCount(0), limitOffset(0)
{
    int numProperties = unserializer->readNumber();
    QByteArray in = unserializer->device()->read(2);
    Q_ASSERT(in == ":{");
    for (int i=0; i < numProperties; ++i) {
        QByteArray varName = unserializer->readString();
        varName = varName.replace('\0', "");
        if (varName == "Vps_Model_Select_OnlyExpr_where") {
            int entries = unserializer->readArrayStart();
            for (int i=0; i < entries; ++i) {
                unserializer->readInt(); //array key
                where << SelectExpr::create(unserializer);
            }
            unserializer->readArrayEnd();
        } else if (varName == "Vps_Model_Select_OnlyExpr_limitCount") {
            limitCount = unserializer->readInt();
        } else if (varName == "Vps_Model_Select_OnlyExpr_limitOffset") {
            limitOffset = unserializer->readInt();
        } else if (varName == "Vps_Model_Select_OnlyExpr_other") {
            int entries = unserializer->readArrayStart();
            for (int i=0; i < entries; ++i) {
                unserializer->readInt(); //array key
                other << unserializer->readRawData();
            }
            unserializer->readArrayEnd();
        }
    }
    in = unserializer->device()->read(1);
    Q_ASSERT(in == "}");
}



Select::~Select()
{
    qDeleteAll(where);
}

bool Select::match(ComponentData* data, ComponentData *parentData) const
{
    //das ist nicht immer korrekt, bei vererben unique nicht
    if (!parentData) parentData = data->parent();

    foreach (SelectExpr *e, where) {
        if (!e->match(data, parentData)) return false;
    }

    if (!other.isEmpty()) {
        if (!m_IdsCache.contains(data->parent())) {
            IndexedString gen;
            foreach (SelectExpr *e, where) {
                if (dynamic_cast<SelectExprWhereGenerator*>(e)) {
                    gen = static_cast<SelectExprWhereGenerator*>(e)->generator();
                    break;
                }
            }
            Q_ASSERT(!gen.isEmpty());
            Generator *generator = 0;
            foreach (Generator *g, Generator::generators(data->root())) {
                if (g->componentClass == data->parent()->componentClass() && g->key == gen) {
                    generator = g;
                    break;
                }
            }
            Q_ASSERT(generator);
            Q_ASSERT(dynamic_cast<GeneratorWithModel*>(generator));
            const_cast<Select*>(this)->m_IdsCache[data->parent()] = static_cast<GeneratorWithModel*>(generator)
                                                ->fetchIds(data->parent(), *this);
        }
        bool ok;
        int childId = data->childId().toInt(&ok);
        if (!ok) return false;
        if (!(m_IdsCache[data->parent()].contains(childId))) return false;
    }
    return true;
}


bool Select::couldCreateIndirectly(const ComponentDataRoot *root, const ComponentClass& cls) const
{
    Q_ASSERT(!cls.isEmpty());

    //qDebug() << cls << *this;
    QPair<const ComponentDataRoot*, ComponentClass> cacheKey = qMakePair<const ComponentDataRoot*, ComponentClass>(root, cls);

    bool ret = false;
    if (const_cast<Select*>(this)->m_couldCreateIndirectlyCache.contains(cacheKey)) {
        ret = const_cast<Select*>(this)->m_couldCreateIndirectlyCache[cacheKey];
//         qDebug() << "Select::couldCreateIndirectly (cached)" << cls << ret << *this;
        return ret;
    }
    const_cast<Select*>(this)->m_couldCreateIndirectlyCache.insert(cacheKey, false);

    ret = true;
    foreach (SelectExpr *e, where) {
        if (!e->mightMatch(cls)) {
            ret = false;
        }
    }
    if (ret) goto cacheAndReturn;

    foreach (Generator *g, Generator::generators(root)) {
        if (g->componentClass == cls) {
            foreach (const ComponentClass &cc, g->childComponentClasses()) {
                if (!cc.isEmpty() && couldCreateIndirectly(root, cc)) {
                    ret = true;
                    goto cacheAndReturn;
                }
            }
        }
    }
cacheAndReturn:
    const_cast<Select*>(this)->m_couldCreateIndirectlyCache.insert(cacheKey, ret);
//     qDebug() << "Select::couldCreateIndirectly" << cls << ret << *this;
    return ret;
}


QList<ComponentData*> Select::filter(const QList<ComponentData*>& data, ComponentData *parentData) const
{
    QList<ComponentData*> ret;
    int i=0;
    foreach (ComponentData *d, data) {
        if (match(d, parentData)) {
            i++;
            if (i > limitOffset) ret << d;
        }
        if (ret.count() == limitCount) break;
    }
    return ret;
}


QDebug operator<<(QDebug dbg, const Select& s)
{
    foreach (SelectExpr *e, s.where) {
        dbg.nospace() << *e;
    }
    if (!s.other.isEmpty()) dbg.space() << "other";
    foreach (const QByteArray &a, s.other) {
        dbg.space() << a;
    }
    if (s.limitCount) dbg.space() << "limitCount" << s.limitCount;
    if (s.limitOffset) dbg.space() << "limitOffset" << s.limitOffset;
    return dbg.space();
}

QByteArray serialize(const Select &s)
{
    QByteArray ret;
    QByteArray cls("Vps_Component_Select");
    ret += "O:"+QByteArray::number(cls.length())+":\""+cls+"\":1:{";
    QHash<IndexedString, RawData> parts;
    parts[IndexedString("where")] = RawData(serialize(s.where));
    parts[IndexedString("limitCount")] = RawData(serialize(s.limitCount));
    parts[IndexedString("limitOffset")] = RawData(serialize(s.limitOffset));
    parts[IndexedString("other")] = RawData(serialize(s.other));
    ret += serializePrivateObjectProperty("_parts", "*", parts);
    ret += "}";
    return ret;
}

