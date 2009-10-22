
#include "Unserializer.h"
#include <QVariant>

QByteArray Unserializer::readRawData()
{
    qint64 startPos = m_device->pos();
    QByteArray type = m_device->peek(2);
    if (type == "i:") {
        readInt();
    } else if (type == "s:") {
        readString();
    } else if (type == "O:") {
        readObjectClassName();
        int cnt = readNumber();
        QByteArray in = m_device->read(2);
        Q_ASSERT(in == ":{");
        for(int i=0; i<cnt; ++i) {
            in = readString(); //var name
            readRawData(); //value
        }
        in = m_device->read(1);
        Q_ASSERT(in == "}");
    } else if (type == "a:") {
        int cnt = readArrayStart();
        for(int i=0; i<cnt; ++i) {
            readRawData(); //key
            readRawData(); //value
        }
        readArrayEnd();
    } else if (type == "b:") {
        readBool();
    } else if (type == "N;") {
        m_device->seek(m_device->pos()+2);
    } else {
        qDebug() << type;
        Q_ASSERT(0);
    }
    qint64 len = m_device->pos() - startPos;
    m_device->seek(startPos);
    return m_device->read(len);
}


QVariant Unserializer::readVariant()
{
    QByteArray type = m_device->peek(2);
    if (type == "i:") {
        return readInt();
    } else if (type == "s:") {
        return readString();
    } else if (type == "O:") {
        qDebug() << "object not yet implemented";
        Q_ASSERT(0);
    } else if (type == "a:") {
        qDebug() << "array not yet implemented";
        Q_ASSERT(0);
    } else if (type == "b:") {
        return readBool();
    } else if (type == "N;") {
        m_device->seek(m_device->pos()+2);
        return QVariant();
    } else {
        qDebug() << type;
        Q_ASSERT(0);
    }
}

