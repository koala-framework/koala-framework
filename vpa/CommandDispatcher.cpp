#include "CommandDispatcher.h"

#include <QBuffer>

#include "ComponentData.h"
#include "Unserializer.h"

#define debug(x) x
#define debugVerbose(x)


void CommandDispatcher::dispatchCommand(const QByteArray& cmd, QByteArray args, QIODevice* socket)
{
    QByteArray prettyArgs = args;
    prettyArgs.replace('\0', "\\0");
    debug(qDebug() << cmd ; )
    debugVerbose(qDebug() << prettyArgs; )

    QBuffer buffer(&args);
    buffer.open(QIODevice::ReadOnly);
    Unserializer u(&buffer);
    int paramCount = u.readArrayStart();

    if (cmd == "ping") {
        Q_ASSERT(paramCount == 0);

        socket->write(serialize("pong"));
    } else if (cmd == "getComponentDataById") {
        Q_ASSERT(paramCount == 1);
        u.readInt(); //array key

        ComponentData *d = ComponentData::getComponentById(QString::fromUtf8(u.readString()));
        if (!d) {
            debugVerbose( qDebug() << "invalid"; )
            socket->write(serialize(NullValue()));
            return;
        }
        QHash<QByteArray, QVariant> data = d->dataForWeb();

        socket->write("a:");
        socket->write(QByteArray::number(data.count()));
        socket->write(":{");
        QHashIterator<QByteArray, QVariant> i(data);
        while (i.hasNext()) {
            i.next();
            socket->write(serialize(i.key()));
            socket->write(serialize(i.value()));
        }
        socket->write("}");
    } else if (cmd == "getComponentsByIds") {
        Q_ASSERT(paramCount == 1);
        u.readInt(); //array key

        QList<ComponentData*> ret;

        int cnt = u.readArrayStart();
        for (int i=0; i<cnt; ++i) {
            u.readInt(); //array key;
            ComponentData *d = ComponentData::getComponentById(QString::fromUtf8(u.readString()));
            if (d) ret << d;
        }
        u.readArrayEnd();

        socket->write(serialize(ret));
    } else if (cmd == "getComponentById") {
        Q_ASSERT(paramCount == 1);
        u.readInt(); //array key

        ComponentData *d = ComponentData::getComponentById(QString::fromUtf8(u.readString()));
        socket->write(serialize(d));
    } else if (cmd == "getComponentsByDbId") {
        Q_ASSERT(paramCount == 2);
        u.readInt(); //array key


        QString id = QString::fromUtf8(u.readString());

        u.readInt(); //array key
        u.readObjectClassName();
        Select s(&u);
        qDebug() << s;

        socket->write(serialize(s.filter(ComponentData::getComponentsByDbId(id), 0)));
    } else if (cmd == "getComponentsBySameClasses") {
        Q_ASSERT(paramCount == 2);
        u.readInt(); //array key
        QList<QByteArray> classes = u.readString().split(',');

        u.readInt(); //array key
        u.readObjectClassName();
        Select s(&u);

        QList< ComponentData* > ret;
        foreach (const QByteArray &c, classes) {
            foreach (ComponentData *d, ComponentData::getComponentsByClass(ComponentClass(QString::fromUtf8(c)))) {
                if (s.match(d, 0)) {
                    ret << d;
                }
            }
        }
        socket->write(serialize(ret));
    } else if (cmd == "getComponentsByClass") {
        Q_ASSERT(paramCount == 2);
        u.readInt(); //array key

        QList<ComponentClass> classes = ComponentClass::getComponentClassesByParentClass(IndexedString(u.readString()));

        u.readInt(); //array key
        u.readObjectClassName();
        Select s(&u);
        qDebug() << s;

        QList<ComponentData*> ret;
        foreach (const ComponentClass &c, classes) {
            foreach (ComponentData *d, ComponentData::getComponentsByClass(c)) {
                if (s.match(d, 0)) {
                    ret << d;
                }
            }
        }
        socket->write(serialize(ret));
    } else if (cmd == "getChildComponents") {
        Q_ASSERT(paramCount == 2);

        u.readInt(); //array key
        QString componentId(QString::fromUtf8(u.readString()));

        u.readInt(); //array key
        u.readObjectClassName();
        Select s(&u);


        ComponentData *d = ComponentData::getComponentById(componentId);
        Q_ASSERT(d);

        debugVerbose( qDebug() << s; )

        socket->write(serialize(d->childComponents(s)));
    } else if (cmd == "countChildComponents") {
        Q_ASSERT(paramCount == 2);

        u.readInt(); //array key
        QString componentId(QString::fromUtf8(u.readString()));

        u.readInt(); //array key
        u.readObjectClassName();
        Select s(&u);


        ComponentData *d = ComponentData::getComponentById(componentId);
        Q_ASSERT(d);

        socket->write(serialize(d->childComponents(s).count()));
    } else if (cmd == "getRecursiveChildComponents") {
        Q_ASSERT(paramCount == 3);

        u.readInt(); //array key
        QString componentId(QString::fromUtf8(u.readString()));

        u.readInt(); //array key
        u.readObjectClassName();
        Select s(&u);
        debugVerbose( qDebug() << "select" << s; )


        u.readInt(); //array key
        u.readObjectClassName();
        Select childSelect(&u);
        debugVerbose( qDebug() << "childSelect" << childSelect; )



        ComponentData *d = ComponentData::getComponentById(componentId);
        if (!d) {
            qWarning() << "invalid componentId" << componentId;
        }
        Q_ASSERT(d);
        socket->write(serialize(d->recursiveChildComponents(s, childSelect)));

    } else if (cmd == "getComponentSetting") {
        Q_ASSERT(paramCount == 2);
        u.readInt(); //array key
        IndexedString cls(u.readString());
        Q_ASSERT(!cls.isEmpty());
        ComponentClass componentClass(cls);

        u.readInt(); //array key
        QString setting(u.readString());

        socket->write(componentClass.getSetting(setting));

    } else if (cmd == "getHasComponentSetting") {
        Q_ASSERT(paramCount == 2);
        u.readInt(); //array key
        IndexedString cls(u.readString());
        Q_ASSERT(!cls.isEmpty());
        ComponentClass componentClass(cls);

        u.readInt(); //array key
        QString setting(u.readString());

        socket->write(serialize(componentClass.hasSetting(setting)));

    } else if (cmd == "getComponentClasses") {
        Q_ASSERT(paramCount == 0);

        socket->write(serialize(ComponentClass::componentClasses()));

    } else if (cmd == "getChildPageByPath") {
        Q_ASSERT(paramCount == 2);

        u.readInt(); //array key
        QString componentId(QString::fromUtf8(u.readString()));

        u.readInt(); //array key
        QString path = QString::fromUtf8(u.readString());

        ComponentData *d = ComponentData::getComponentById(componentId);
        Q_ASSERT(d);
        socket->write(serialize(d->childPageByPath(path)));

    } else if (cmd == "handleChangedRows") {
        Q_ASSERT(paramCount == 1);

        u.readInt(); //array key

        int cnt = u.readArrayStart();
        for (int i=0; i<cnt; ++i) {
            Generator::ChangedRowMethod method;
            {
                QString m = u.readString();
                if (m == "update") {
                    method = Generator::RowUpdated;
                } else if (m == "insert") {
                    method = Generator::RowInserted;
                } else if (m == "delete") {
                    method = Generator::RowDeleted;
                } else {
                    Q_ASSERT(0);
                }
            }
            int cntRows = u.readArrayStart();
            for (int j=0; j<cntRows; ++i) {
                u.readInt(); //array key
                int cntFields = u.readArrayStart();
                IndexedString model;
                QString id;
                for (int k=0; k<cntFields; ++k) {
                    if (u.readString() == "model") {
                        model = IndexedString(u.readString());
                    } else if (u.readString() == "id") {
                        id = u.readString();
                    } else {
                        Q_ASSERT(0);
                        u.readVariant();
                    }
                }
                u.readArrayEnd();
                Generator::handleChangedRow(method, model, id);
            }
            u.readArrayEnd();
        }
        u.readArrayEnd();
        socket->write(serialize(true));
    } else {
        socket->write("ERROR: unknown command");
    }
    u.readArrayEnd();
}
