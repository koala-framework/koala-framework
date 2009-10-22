#ifndef PHPPROCESS_H
#define PHPPROCESS_H

#include <QtCore/QStringList>
#include <QtCore/QProcess>

#define ifDebugProcess(x)


class PhpProcess
{
private:
    PhpProcess(QString webDir);
public:
    static void setup(QString webDir)
    {
        i = new PhpProcess(webDir);
    }
    static PhpProcess *getInstance()
    {
        return i;
    }

    QByteArray call(QByteArray method, const QList<QByteArray> &arguments = QList<QByteArray>());

private:
    static PhpProcess *i;
    QProcess p;
};

#endif
