#ifndef COMPONENTDATAROOT_H
#define COMPONENTDATAROOT_H

#include <QThread>

#include "ComponentData.h"
#include "ConnectionThread.h"

struct ComponentDataRoot : public ComponentData
{
    ComponentDataRoot(IndexedString componentClass)
        : ComponentData(0, 0, QString("root"), QString("root"), componentClass)
    {
    }
    
    ~ComponentDataRoot();
    /* erstmal ned aktivieren
    static ComponentDataRoot *getInstance()
    {
        Q_ASSERT(qobject_cast<ConnectionThread*>(QThread::currentThread()));
        return getInstance(static_cast<ConnectionThread*>(QThread::currentThread())->rootComponentClass());
    }
    */
    static ComponentDataRoot *getInstance(IndexedString componentClass)
    {
        if (!m_instances.contains(componentClass)) {
            initInstance(componentClass);
        }
        return m_instances[componentClass];
    }

    static bool hasInstance(IndexedString componentClass)
    {
        return m_instances.contains(componentClass);
    }

private:
    static void initInstance(IndexedString componentClass);
    static QHash<IndexedString, ComponentDataRoot*> m_instances;
};

#endif
