#include "PhpProcess.h"

#include <QtCore/QDebug>

PhpProcess *PhpProcess::i = 0;
QByteArray PhpProcess::call(QByteArray method, const QList< QByteArray >& arguments)
{
    QByteArray ret;
    foreach (const QByteArray& a, arguments) {
        method += "$$$$" + a;
    }
    ifDebugProcess( qDebug() << "calling php" << method; )
    p.write(method + "\n");
    while (true) {
        if (p.state() != QProcess::Running) {
            qDebug() << ret << p.readAll();
            qDebug() << p.readAllStandardError();
            qFatal("php process exited!");
        }
        p.waitForReadyRead();
        QByteArray r = p.readAll();
        ifDebugProcess( qDebug() << "php returned" << r; )
        ret.append(r);
        ifDebugProcess( qDebug() << "last byte" << (int)ret.at(ret.length()-1); )
        if (ret.at(ret.length()-1) == 0) break;
    }
    ret = ret.left(ret.length()-1);
    ifDebugProcess( qDebug() << "returning"; )
    return ret;
}

PhpProcess::PhpProcess(QString webDir)
{
    p.setProcessChannelMode(QProcess::MergedChannels);
    p.setWorkingDirectory(webDir);
    QStringList arg;
    arg << "bootstrap.php" << "get-generators";
    ifDebugProcess( qDebug() << "starting php process"; )
    p.start("php", arg);
    p.waitForStarted();
}
