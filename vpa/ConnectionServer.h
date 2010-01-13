#ifndef CONNECTIONSERVER_H
#define CONNECTIONSERVER_H

#include <QLocalServer>


class ConnectionServer : public QLocalServer
{
    Q_OBJECT

public:
    ConnectionServer(QObject *parent = 0);

protected:
    virtual void incomingConnection(quintptr socketDescriptor);
};

#endif
