
#ifndef INDEXEDSTRING_H
#define INDEXEDSTRING_H

#include <QtCore/QString>
#include <QtCore/QHash>
#include <QtCore/QDebug>

#include "Serialize.h"

class IndexedString
{
public:
    IndexedString(const QString &string)
    {
        m_index = m_strings.key(string);
        if (!m_index) {
            m_nextId++;
            m_index = m_nextId;
            m_strings[m_index] = string;
        }
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
