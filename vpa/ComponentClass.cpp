#include "ComponentClass.h"
#include "PhpProcess.h"
#include <qxmlstream.h>
#include "Unserializer.h"
#include <qbuffer.h>


QHash<IndexedString, ComponentClassData> ComponentClass::m_data;
QHash<QString, ComponentClass> ComponentClass::m_shortcutUrlToComponent;
QHash<ComponentClass, IndexedString> ComponentClass::m_componentToShortcutUrl;

void ComponentClass::init()
{
    QXmlStreamReader xml(PhpProcess::getInstance()->call("get-component-classes"));
    while (!xml.atEnd()) {
        xml.readNext();
        if (xml.isStartElement() && xml.name() == "componentClass") {
            IndexedString shortcutUrl;
            IndexedString componentClass(xml.attributes().value("class").toString());
            ComponentClassData data;
            while (!xml.atEnd()) {
                if (xml.isStartElement() && xml.name() == "flag") {
                    IndexedString flagName(xml.attributes().value("name").toString());
                    QString text = xml.readElementText();
                    QVariant value;
                    if (text == "true") {
                        value = true;
                    } else if (text == "false") {
                        value = false;
                    } else {
                        value = text;
                    }
                    data.flags[flagName] = value;
                }
                if (xml.isStartElement() && xml.name() == "settings") {
                    QByteArray d = xml.readElementText().toUtf8();
                    d.replace("\\0", QByteArray("\0", 1));
                    QBuffer b(&d);
                    b.open(QIODevice::ReadOnly);
                    Unserializer s(&b);
                    int len = s.readArrayStart();
                    for (int i=0; i < len; ++i) {
                        IndexedString key(s.readString());
                        QByteArray value(s.readString());
                        data.settings[key] = value;
                        if (key == IndexedString("shortcutUrl")) {
                            QBuffer buffer(&value);
                            buffer.open(QIODevice::ReadOnly);
                            Unserializer u(&buffer);
                            shortcutUrl = IndexedString(u.readString());
                        }
                    }
                    s.readArrayEnd();
                }

                if (xml.isStartElement() && xml.name() == "parentClass") {
                    data.m_parentClasses << IndexedString(xml.readElementText());
                }
                if (xml.isEndElement() && xml.name() == "componentClass") {
                    break;
                }
                xml.readNext();
            }
            Q_ASSERT(!componentClass.isEmpty());
            m_data[componentClass] = data;
            if (!shortcutUrl.isEmpty()) {
                m_shortcutUrlToComponent[shortcutUrl.toString()] = ComponentClass(componentClass);
                m_componentToShortcutUrl[ComponentClass(componentClass)] = shortcutUrl;
            }
        }
    }
}

QDebug operator<<(QDebug dbg, const ComponentClass& s)
{
    dbg.nospace() << s.toString();
    return dbg.space();
}



QList<ComponentClass> ComponentClass::getComponentClassesByParentClass(IndexedString parent)
{
    QList<ComponentClass> ret;
    QHashIterator<IndexedString, ComponentClassData> i(m_data);
    while (i.hasNext()) {
        i.next();
        if (parent == i.key() || i.value().m_parentClasses.contains(parent)) {
            ret << ComponentClass(i.key());
        }
    }
    return ret;
}

IndexedString ComponentClass::shortcutUrl()
{
    if (m_componentToShortcutUrl.contains(*this)) {
        return m_componentToShortcutUrl[*this];
    }
    return IndexedString();
}


ComponentClass ComponentClass::componentForShortcutUrl(const QString& url)
{
    if (m_shortcutUrlToComponent.contains(url)) {
        return m_shortcutUrlToComponent[url];
    }
    return ComponentClass();
}

