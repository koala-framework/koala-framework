#ifndef SELECT_H
#define SELECT_H
#include "SelectExpr.h"

class Unserializer;

class Select {
public:
    Select(Unserializer *unserializer);
    Select();
    Select(const Select &s) {
        where = s.where;
        limitCount = s.limitCount;
        limitOffset = s.limitOffset;
        other = s.other;
    }

    ~Select();

    bool match(ComponentData *data, ComponentData *parentData) const;
    bool couldCreateIndirectly(const ComponentDataRoot *root, const ComponentClass &cls) const;

    QList<ComponentData*> filter(const QList<ComponentData*>& data, ComponentData *parentData) const;

public:
    QList<SelectExpr*> where;
    int limitCount;
    int limitOffset;
    QList<RawData> other;

private:
    QHash<ComponentData *, QList<int> > m_IdsCache;
    QHash<QPair<const ComponentDataRoot *, ComponentClass>, bool> m_couldCreateIndirectlyCache;
};
QDebug operator<<(QDebug dbg, const Select &s);
QByteArray serialize(const Select &s);

#endif
