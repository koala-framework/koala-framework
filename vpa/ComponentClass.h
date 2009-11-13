#ifndef COMPONENTCLASS_H
#define COMPONENTCLASS_H

#include <QtCore/QHash>
#include <QtCore/QVariant>
#include <QtCore/QMutex>

#include "IndexedString.h"

class ComponentDataRoot;
class ComponentClass;
class ComponentClassData
{
    friend class ComponentClass;
private:
    QHash<IndexedString, QVariant> flags;
    QList<IndexedString> m_parentClasses;
    QHash<IndexedString, QByteArray> settings;
    QList<IndexedString> m_editComponents;
    QList<IndexedString> m_plugins;
};

class ComponentClass
{
public:
    static void init(const IndexedString &rootComponentClass);

    ComponentClass()
    {
    }

    ComponentClass(QString cls) : m_componentClass(cls)
    {
        QMutexLocker locker(&m_dataMutex);
        Q_ASSERT(!cls.isEmpty());
        Q_ASSERT(m_data.contains(m_componentClass));
    }

    ComponentClass(IndexedString cls) : m_componentClass(cls)
    {
        QMutexLocker locker(&m_dataMutex);
        Q_ASSERT(!cls.isEmpty());
        if (!m_data.contains(m_componentClass)) {
            qWarning() << "not loaded ComponentClass" << m_componentClass;
        }
        Q_ASSERT(m_data.contains(m_componentClass));
    }

    ComponentClass( const ComponentClass& rhs)
    {
        m_componentClass = rhs.m_componentClass;
    }


    inline QHash<IndexedString, QVariant> flags() const
    {
        QMutexLocker locker(&m_dataMutex);
        Q_ASSERT(!m_componentClass.isEmpty());
        return m_data[m_componentClass].flags;
    }

    inline QList<IndexedString> parentClasses() const
    {
        QMutexLocker locker(&m_dataMutex);
        Q_ASSERT(!m_componentClass.isEmpty());
        return m_data[m_componentClass].m_parentClasses;
    }

    inline QList<IndexedString> editComponents() const
    {
        QMutexLocker locker(&m_dataMutex);
        Q_ASSERT(!m_componentClass.isEmpty());
        return m_data[m_componentClass].m_editComponents;
    }

    inline QList<IndexedString> plugins() const
    {
        QMutexLocker locker(&m_dataMutex);
        Q_ASSERT(!m_componentClass.isEmpty());
        return m_data[m_componentClass].m_plugins;
    }

    inline bool hasFlag(IndexedString flag) const
    {
        QMutexLocker locker(&m_dataMutex);
        Q_ASSERT(!m_componentClass.isEmpty());
        if (!m_data[m_componentClass].flags.contains(flag)) return false;
        if (m_data[m_componentClass].flags[flag].type() == QVariant::Bool) {
            if (!m_data[m_componentClass].flags[flag].toBool()) return false;
        }
        return true;
    }

    inline bool operator == ( const ComponentClass& rhs ) const {
        return m_componentClass == rhs.m_componentClass;
    }
    inline bool operator != ( const ComponentClass& rhs ) const {
        return m_componentClass != rhs.m_componentClass;
    }

    QString toString() const
    {
        return m_componentClass.toString();
    }

    inline IndexedString toIndexedString() const
    {
        return m_componentClass;
    }

    inline bool isEmpty() const
    {
        return m_componentClass.isEmpty();
    }

    QByteArray getSetting(IndexedString name)
    {
        QMutexLocker locker(&m_dataMutex);
        Q_ASSERT(!m_componentClass.isEmpty());
        if (!m_data[m_componentClass].settings.contains(name)) return QByteArray();
        return m_data[m_componentClass].settings[name];
    }

    bool hasSetting(IndexedString name)
    {
        QMutexLocker locker(&m_dataMutex);
        Q_ASSERT(!m_componentClass.isEmpty());
        return m_data[m_componentClass].settings.contains(name);
    }

    IndexedString shortcutUrl();
    static ComponentClass componentForShortcutUrl(const QString &url);

    static QList<ComponentClass> getComponentClassesByParentClass(IndexedString parent);

private:
    IndexedString m_componentClass;
    static QHash<IndexedString, ComponentClassData> m_data;

    static QHash<QString, ComponentClass> m_shortcutUrlToComponent;
    static QHash<ComponentClass, IndexedString> m_componentToShortcutUrl;

    static QMutex m_dataMutex;
};

QDebug operator<<(QDebug dbg, const ComponentClass &s);

inline uint qHash(const ComponentClass &i)
{
    return qHash(i.toIndexedString());
};

#endif
