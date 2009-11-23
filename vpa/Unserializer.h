#ifndef UNSERIALIZER_H
#define UNSERIALIZER_H

#include <QIODevice>
#include <QDebug>

class Unserializer
{
public:
    Unserializer(QIODevice *device) : m_device(device) {}

    int readArrayStart()
    {
        QByteArray in = device()->read(2);
        if (in != "a:") {
            qWarning() << in << device()->peek(1024);
        }
        Q_ASSERT(in == "a:");
        int ret = readNumber();
        in = device()->read(2);
        Q_ASSERT(in == ":{");
        return ret;
    }

    void readArrayEnd()
    {
        QByteArray in = device()->read(1);
        Q_ASSERT(in == "}");
    }

    QByteArray readObjectClassName() {
        QByteArray in = m_device->read(2);
        Q_ASSERT(in == "O:");
        QByteArray ret = readLengthWithString();
        in = m_device->read(1);
        Q_ASSERT(in == ":");
        return ret;
    }

    QByteArray readLengthWithString() {
        
        int len = readNumber();
        QByteArray in = m_device->read(2);
        Q_ASSERT(in == ":\"");
        QByteArray ret = m_device->read(len);
        Q_ASSERT(ret.length() == len);
        
        in = m_device->read(1);
        Q_ASSERT(in == "\"");
        return ret;
    }

    QByteArray readString()
    {
        QByteArray in = m_device->read(2);
        if (in != "s:") {
            qWarning() << in;
        }
        Q_ASSERT(in == "s:");
        QByteArray ret = readLengthWithString();
        in = m_device->read(1);
        Q_ASSERT(in == ";");
        return ret;
    }

    int readInt()
    {
        QByteArray in = m_device->read(2);
        Q_ASSERT(in == "i:");
        int ret = readNumber();
        in = m_device->read(1);
        Q_ASSERT(in == ";");
        return ret;
    }

    bool readBool()
    {
        QByteArray in = m_device->read(2);
        Q_ASSERT(in == "b:");
        int ret = readNumber();
        in = m_device->read(1);
        Q_ASSERT(in == ";");
        return ret;
    }

    int readNumber()
    {
        QByteArray number;
        char c;
        while (m_device->getChar(&c)) {
            QChar qc(c);
            if (qc.isNumber()) {
                number.append(c);
            } else {
                m_device->ungetChar(c);
                break;
            }
        }
        return number.toInt();
    }

    QByteArray readRawData();
    QVariant readVariant();

    inline QIODevice *device() const { return m_device; }

private:
    QIODevice *m_device;
};

#endif
