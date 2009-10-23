
#ifndef INDEXEDSTRING_H
#define INDEXEDSTRING_H

#include <QtCore/QString>
#include <QtCore/QHash>
#include <QtCore/QDebug>
#include <QtCore/QReadWriteLock>

#include "Serialize.h"

class IndexedString
{
private:
    static QReadWriteLock lock;
public:
    IndexedString(const QString &string)
    {
        lock.lockForRead();
        m_index = m_strings.key(string);
        if (!m_index) {
            lock.unlock();
            lock.lockForWrite();
            m_nextId++;
            m_index = m_nextId;
            m_strings[m_index] = string;
        }
        lock.unlock();
    }

    IndexedString()
    {
        m_index = 0;
    }

    IndexedString( const IndexedString& rhs)
    {
        m_index = rhs.m_index;
    }

    inline bool operator == ( const IndexedString& rhs ) const {
        return m_index == rhs.m_index;
    }
    inline bool operator != ( const IndexedString& rhs ) const {
        return m_index != rhs.m_index;
    }

    inline QString toString() const {
        QReadLocker locker(&lock);
        return m_strings[m_index];
    }

    inline bool isEmpty() const {
        return m_index == 0;
    }

    inline uint index() const {
        return m_index;
    }

private:
    uint m_index;
    static QHash<uint, QString> m_strings;
    static uint m_nextId;
};


inline uint qHash(const IndexedString &str)
{
    return str.index();
};

QDebug operator<<(QDebug dbg, const IndexedString &s);

QByteArray serialize(const IndexedString &s);

template <typename T>
QByteArray serialize(QHash<IndexedString, T > v) {
    QByteArray ret;
    ret += "a:";
    ret += QByteArray::number(v.count());
    ret += ":{";
    QHashIterator<IndexedString, T> i(v);
    while (i.hasNext()) {
        i.next();
        ret += serialize(i.key());
        ret += serialize(i.value());
    }
    ret += "}";
    return ret;
}

#endif
