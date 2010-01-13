#ifndef CONNECTIONTHREAD_H
#define CONNECTIONTHREAD_H

#include <qthread.h>
#include "IndexedString.h"


class ConnectionThread : public QThread
{
    Q_OBJECT
public:
    ConnectionThread(quintptr socketDescriptor, QObject *parent);

    virtual void run();

    void componentCreated();
    IndexedString rootComponentClass();
    void setCheckCountComponentsCreated(bool v);

private:
    quintptr m_socketDescriptor;
    int m_countComponentsCreated;
    bool m_checkCountComponentsCreated;

    IndexedString m_rootComponentClass;
};

#endif
