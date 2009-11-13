#ifndef CONNECTIONTHREAD_H
#define CONNECTIONTHREAD_H

#include <qthread.h>
#include "IndexedString.h"


class ConnectionThread : public QThread
{
    Q_OBJECT
public:
    ConnectionThread(int socketDescriptor, QObject *parent);

    virtual void run();

    void componentCreated();
    IndexedString rootComponentClass();

private:
    int m_socketDescriptor;
    int m_countComponentsCreated;

    IndexedString m_rootComponentClass;
};

#endif
