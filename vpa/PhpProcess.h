#ifndef PHPPROCESS_H
#define PHPPROCESS_H

#include <QtCore/QStringList>
#include "IndexedString.h"

class ComponentDataRoot;
class ProcessThread;

class PhpProcess
{
private:
    PhpProcess(QString webDir);
public:
    static void setup(QString webDir);

    static PhpProcess *getInstance()
    {
        return m_instance;
    }

    QByteArray call(const ComponentDataRoot *root, const QByteArray& method, const QList< QByteArray >& arguments = QList<QByteArray>());
    QByteArray call(const IndexedString &rootComponentClass, const QByteArray& method, const QList< QByteArray >& arguments = QList<QByteArray>());

private:
    static PhpProcess *m_instance;
    ProcessThread *m_processThread;
};

#endif
