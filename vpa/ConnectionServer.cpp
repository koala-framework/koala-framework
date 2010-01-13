#include "ConnectionServer.h"

#include "ConnectionThread.h"

ConnectionServer::ConnectionServer(QObject* parent)
    : QLocalServer(parent)
{
}

void ConnectionServer::incomingConnection(quintptr socketDescriptor)
{
    qDebug() << "ConnectionServer::incomingConnection" << socketDescriptor;
    ConnectionThread *thread = new ConnectionThread(socketDescriptor, this);
    connect(thread, SIGNAL(finished()), thread, SLOT(deleteLater()));
    thread->start();
}
