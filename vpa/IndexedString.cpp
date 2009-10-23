#include "IndexedString.h"

QHash<uint, QString> IndexedString::m_strings;
uint IndexedString::m_nextId = 0;
QReadWriteLock IndexedString::lock;

QDebug operator<<(QDebug dbg, const IndexedString& s)
{
    dbg.nospace() << s.toString();
    return dbg.space();
}

QByteArray serialize(const IndexedString& s) {
    return serialize(s.toString());
}
