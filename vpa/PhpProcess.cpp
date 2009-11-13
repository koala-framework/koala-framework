#include "PhpProcess.h"

#include <QtCore/QDebug>
#include <qcoreapplication.h>
#include <qmutex.h>
#include <QThread>
#include <QProcess>

#define ifDebugProcess(x)
#include "ComponentDataRoot.h"


PhpProcess *PhpProcess::m_instance = 0;

class Process : public QObject
{
    Q_OBJECT
public:
    Process(const QString &webDir, QObject *parent = 0) : QObject(parent)
    {
        p = new QProcess;
        p->setProcessChannelMode(QProcess::MergedChannels);
        p->setWorkingDirectory(webDir);
        QStringList arg;
        arg << "bootstrap.php" << "get-generators";
        ifDebugProcess( qDebug() << "starting php process"; )
        p->start("php", arg);
        p->waitForStarted();
        ifDebugProcess( qDebug() << "started php process"; )
    }

public slots:
    void call(const QByteArray &method)
    {
        ret.clear();
        ifDebugProcess( qDebug() << "calling php" << method; )
        p->write(method + "\n");
        while (true) {
            if (p->state() != QProcess::Running) {
                qDebug() << ret << p->readAll();
                qDebug() << p->readAllStandardError();
                qDebug() << "called" << method;
                qFatal("php process exited!");
            }
            p->waitForReadyRead();
            QByteArray r = p->readAll();
            if (r.isEmpty()) continue;
            //ifDebugProcess( qDebug() << "php returned" << r; )
            ret.append(r);
            //ifDebugProcess( qDebug() << "last byte" << (int)ret.at(ret.length()-1); )
            if (ret.at(ret.length()-1) == 0) break;
        }
        ret = ret.left(ret.length()-1);
        ifDebugProcess( qDebug() << "returning"; )
        retReady.unlock();
    }

public:
    QByteArray ret;
    QMutex retReady;

private:
    QProcess *p;
};

class ProcessThread : public QThread
{
    Q_OBJECT
public:
    
    ProcessThread(const QString &_webDir, QObject* parent = 0)
        : QThread(parent), webDir(_webDir)
    {
        startupMutex.lock();
    }

    virtual void run() {
        process = new Process(webDir);
        startupMutex.unlock();
        exec();
    }

    QByteArray call(const IndexedString& rootComponentClass, QByteArray method, const QList< QByteArray >& arguments)
    {
        startupMutex.lock();
        ifDebugProcess( qDebug() << "calling" << method; )
        method = rootComponentClass.toString().toUtf8() + "$$$$" + method;
        foreach (const QByteArray& a, arguments) {
            method += "$$$$" + a;
        }
        retMutex.lock();
        process->retReady.lock();
        QMetaObject::invokeMethod(process, "call", Qt::QueuedConnection, Q_ARG(QByteArray, method));
        process->retReady.lock(); //wait for ret
        QByteArray ret = process->ret;
        process->retReady.unlock();
        retMutex.unlock();
        startupMutex.unlock();
        return ret;
    }
private:
    QString webDir;
    Process *process;
    QMutex retMutex;
    QMutex startupMutex;
};



void PhpProcess::setup(QString webDir)
{
    m_instance = new PhpProcess(webDir);
}

PhpProcess::PhpProcess(QString webDir)
{
    m_processThread = new ProcessThread(webDir);
    m_processThread->start();
}


QByteArray PhpProcess::call(const ComponentDataRoot *root, const QByteArray &method, const QList< QByteArray >& arguments)
{
    if (root) {
        return call(root->componentClass().toIndexedString(), method, arguments);
    } else {
        return call(IndexedString(""), method, arguments);
    }
}


QByteArray PhpProcess::call(const IndexedString& rootComponentClass, const QByteArray& method, const QList< QByteArray >& arguments)
{
    Q_ASSERT(m_processThread->isRunning());
    return m_processThread->call(rootComponentClass, method, arguments);
}


#include "PhpProcess.moc"

