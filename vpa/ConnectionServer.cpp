#include "ConnectionServer.h"

#include "ConnectionThread.h"

ConnectionServer::ConnectionServer(QObject* parent)
    : QTcpServer(parent)
{
}

void ConnectionServer::incomingConnection(int socketDescriptor)
{
    qDebug() << "incomingConnection" << socketDescriptor;
    ConnectionThread *thread = new ConnectionThread(socketDescriptor, this);
    connect(thread, SIGNAL(finished()), thread, SLOT(deleteLater()));
    thread->start();
}
