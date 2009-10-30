#ifndef SELECT_H
#define SELECT_H
#include "SelectExpr.h"

class Unserializer;

class Select {
public:
    Select(Unserializer *unserializer);
    Select();

    ~Select();

    bool match(ComponentData *data) const;
    QList<ComponentData*> filter(const QList<ComponentData*>& data) const;

public:
    QList<SelectExpr*> where;
    int limitCount;
    int limitOffset;
    QList<RawData> other;

private:
    QHash<ComponentData *, QList<int> > m_IdsCache;
};
QDebug operator<<(QDebug dbg, const Select &s);
QByteArray serialize(const Select &s);

#endif
