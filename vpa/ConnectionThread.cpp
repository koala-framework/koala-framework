#include "ConnectionThread.h"

#include <QTcpSocket>
#include <QTime>

#include "ComponentData.h"
#include "CommandDispatcher.h"
#include "ComponentDataRoot.h"


#define debugCommandProfiler

#ifdef debugCommandProfiler
struct ProfilerData {
    QByteArray cmd;
    QByteArray args;
    int time;
};
#endif

ConnectionThread::ConnectionThread(int socketDescriptor, QObject* parent)
: QThread(parent), m_socketDescriptor(socketDescriptor),
    m_countComponentsCreated(0), m_checkCountComponentsCreated(false)
{
}


void ConnectionThread::componentCreated()
{
    m_countComponentsCreated++;
    if (m_checkCountComponentsCreated) {
//         Q_ASSERT(m_countComponentsCreated < 10000);
    }
}


IndexedString ConnectionThread::rootComponentClass()
{
    return m_rootComponentClass;
}


void ConnectionThread::run()
{
    QTcpSocket socket;
    if (!socket.setSocketDescriptor(m_socketDescriptor)) {
        qWarning() << socket.errorString();
        Q_ASSERT(0);
        return;
    }
    //qDebug() << "new connection";

    #ifdef debugCommandProfiler
    QList<ProfilerData> profiler;
    #endif

    socket.waitForConnected();

    QByteArray rc;
    do {
        if (socket.state() == QAbstractSocket::UnconnectedState) break;
        if (socket.waitForReadyRead()) {
            rc.append(socket.readAll());
        }
    } while(!rc.endsWith('\0'));
    socket.write("\0\n", 2);
    socket.flush();
    rc.chop(1);
    QByteArray prettyRc = rc;
    prettyRc.replace('\0', "\\0");
    m_rootComponentClass = IndexedString(QString(rc));
    qDebug() << "new connection thread with root" << m_rootComponentClass;

    forever {
        QByteArray cmd;
        do {
            if (socket.state() == QAbstractSocket::UnconnectedState) {
                break;
            }
            if (socket.waitForReadyRead()) {
                cmd.append(socket.readAll());
            }
        } while(!cmd.endsWith('\0'));
        if (socket.state() == QAbstractSocket::UnconnectedState) {
            break;
        }
        QTime stopWatch;
        stopWatch.start();
        cmd.chop(1);
        QByteArray args;
        if (cmd.indexOf(' ') != -1) {
            args = cmd.mid(cmd.indexOf(' ')+1);
            cmd = cmd.left(cmd.indexOf(' '));
        }
        const ComponentDataRoot* root = 0;
//         if (cmd != "reset" || ComponentDataRoot::hasInstance(m_rootComponentClass)) {
            root = ComponentDataRoot::getInstance(m_rootComponentClass);
//         }
        CommandDispatcher::dispatchCommand(root, cmd, args, &socket);
        socket.write("\0\n", 2);
        socket.flush();
        socket.waitForBytesWritten();
        #ifdef debugCommandProfiler
        ProfilerData pd;
        pd.time = stopWatch.elapsed();
        pd.cmd = cmd;
        pd.args = args;
        profiler << pd;
        #endif
        //qDebug() << stopWatch.elapsed() << "ms" << ComponentData::count << "datas";
//        qDebug() << "php memory usage" << PhpProcess::getInstance()->call(0, "memory-usage");
        //qDebug() << "";
    }
    #ifdef debugCommandProfiler
    ProfilerData pd;
    int sum = 0;
    int max = 0;
    qDebug() << "Executed commands:";
    foreach (const ProfilerData &pd, profiler) {
        sum += pd.time;
        max = qMax(max, pd.time);
        QByteArray args = pd.args;
        qDebug() << "\n" << pd.cmd << pd.time << "ms" << args.replace('\0', "\\0");
    }
    qDebug() << "commands" << profiler.count() << "sumProcessingTime" << sum << "ms" << "maxProcessingTime" << max << "ms";
    #endif
}

void ConnectionThread::setCheckCountComponentsCreated(bool v)
{
    if (!v) m_countComponentsCreated = 0;
    m_checkCountComponentsCreated = v;
}

