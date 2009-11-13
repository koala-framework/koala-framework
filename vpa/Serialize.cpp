#include "Serialize.h"

#include <QtCore/QStringList>

QByteArray serialize(NullValue) {
    return QByteArray("N;");
}


QByteArray serialize(const RawData& s)
{
    return s;
}


QByteArray serialize(const QByteArray& s) {
    QByteArray ret;
    ret += "s:";
    ret += QByteArray::number(s.length());
    ret += ":\""+s+"\"";
    ret += ';';
    return ret;
}

QByteArray serialize(const QString& s) {
    return serialize(s.toUtf8());
}


QByteArray serialize(bool v) {
    QByteArray ret;
    ret += "b:";
    ret += v ? '1' : '0';
    ret += ';';
    return ret;
}

QByteArray serialize(int v) {
    QByteArray ret;
    ret += "i:";
    ret += QByteArray::number(v);
    ret += ';';
    return ret;
}

QByteArray serialize(QVariant v)
{
    if (v.type() == QVariant::Int) {
        return serialize(v.toInt());
    } else if (v.type() == QVariant::String) {
        return serialize(v.toString());
    } else if (v.type() == QVariant::Bool) {
        return serialize(v.toBool());
    } else if (!v.isValid()) {
        return serialize(NullValue());
    } else if (v.type() == QVariant::ByteArray) {
        return serialize(v.toByteArray());
    } else if (v.type() == QVariant::StringList) {
        return serialize(v.toStringList());
    } else {
        qDebug() << "unknown QVariant type" << v.typeName();
        Q_ASSERT(0);
    }
    return QByteArray();
}
