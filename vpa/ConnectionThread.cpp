#include "ConnectionThread.h"

#include <QTcpSocket>
#include <QTime>

#include "ComponentData.h"
#include "CommandDispatcher.h"

ConnectionThread::ConnectionThread(int socketDescriptor, QObject* parent)
: QThread(parent), m_socketDescriptor(socketDescriptor), m_countComponentsCreated(0)
{
}


void ConnectionThread::componentCreated()
{
    m_countComponentsCreated++;
//     Q_ASSERT(m_countComponentsCreated < 5000);
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

    int commands = 0;
    int sumProcessingTime = 0;
    int maxProcessingTime = 0;

    socket.waitForConnected();
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
        CommandDispatcher::dispatchCommand(cmd, args, &socket);
        socket.write("\0\n", 2);
        socket.flush();
        socket.waitForBytesWritten();
        int t = stopWatch.elapsed();
        commands++;
        sumProcessingTime += t;
        if (t > maxProcessingTime) maxProcessingTime = t;

        //qDebug() << stopWatch.elapsed() << "ms" << ComponentData::count << "datas";
//        qDebug() << "php memory usage" << PhpProcess::getInstance()->call("memory-usage");
        //qDebug() << "";
    }
    qDebug() << "commands" << commands << "sumProcessingTime" << sumProcessingTime << "ms" << "maxProcessingTime" << maxProcessingTime << "ms";
}

