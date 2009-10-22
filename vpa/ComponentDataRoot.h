
#ifndef COMPONENTDATAROOT_H
#define COMPONENTDATAROOT_H

#include "ComponentData.h"

struct ComponentDataRoot : public ComponentData
{
    ComponentDataRoot(IndexedString componentClass)
        : ComponentData(0, 0, QString("root"), QString("root"), componentClass)
    {
    }

    static ComponentDataRoot *getInstance()
    {
        return m_instance;
    }
    static void initInstance(IndexedString componentClass)
    {
        m_instance = new ComponentDataRoot(componentClass);
    }

private:
    static ComponentDataRoot *m_instance;
};

#endif
