#ifndef CONNECTIONSERVER_H
#define CONNECTIONSERVER_H

#include <QTcpServer>


class ConnectionServer : public QTcpServer
{
    Q_OBJECT

public:
    ConnectionServer(QObject *parent = 0);

protected:
    virtual void incomingConnection(int socketDescriptor);
};

#endif
