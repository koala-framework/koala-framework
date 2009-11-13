#ifndef SERIALIZE_H
#define SERIALIZE_H

#include <QByteArray>
#include <QVariant>
#include <QStringList>

#include "IndexedString.h"

class NullValue {};
class RawData : public QByteArray {
public:
    RawData(const QByteArray& a) : QByteArray(a) {}
    RawData() : QByteArray() {}
};

QByteArray serialize(NullValue);
QByteArray serialize(const RawData &s);
QByteArray serialize(const QByteArray &s);
QByteArray serialize(const QString &s);
QByteArray serialize(bool v);
QByteArray serialize(int v);
QByteArray serialize(QVariant v);

template <typename T>
QByteArray serializePrivateObjectProperty(const QByteArray& name, const QByteArray& cls, T value) {
    QByteArray s;
    s += "s:";
    s += QByteArray::number(name.length() + cls.length() + 2);
    s += ':';
    s += '"';
    s += '\0';
    s += cls;
    s += '\0';
    s += name;
    s += '"';
    s += ';';
    s += serialize(value);
    return s;
}

template <typename T>
QByteArray serializeObjectProperty(const QByteArray& name, T value) {
    QByteArray s;
    s += "s:";
    s += QByteArray::number(name.length());
    s += ':';
    s += '"';
    s += name;
    s += '"';
    s += ';';
    s += serialize(value);
    return s;
}

template <typename T>
QByteArray serialize(QList< T > v) {
    QByteArray ret;
    ret += "a:";
    ret += QByteArray::number(v.count());
    ret += ":{";
    for(int i=0; i<v.count(); ++i) {
        ret += "i:" + QByteArray::number(i) + ";";
        ret += serialize(v.at(i));
    }
    ret += "}";
    return ret;
}

template <typename T>
QByteArray serialize(QHash<QString, T > v) {
    QByteArray ret;
    ret += "a:";
    ret += QByteArray::number(v.count());
    ret += ":{";
    QHashIterator<QString, T> i(v);
    while (i.hasNext()) {
        i.next();
        ret += serialize(i.key());
        ret += serialize(i.value());
    }
    ret += "}";
    return ret;
}

#endif
