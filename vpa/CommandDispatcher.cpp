#include "CommandDispatcher.h"

#include <QBuffer>

#include "ComponentData.h"
#include "Unserializer.h"
#include "ComponentDataRoot.h"

#define debug(x)
#define debugVerbose(x)


void CommandDispatcher::dispatchCommand(const ComponentDataRoot* root, const QByteArray& cmd, QByteArray args, QIODevice* socket)
{
    QByteArray prettyArgs = args;
    prettyArgs.replace('\0', "\\0");
    debugVerbose(qDebug() << "********************************************************"; )
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
        Q_ASSERT(paramCount == 1 || paramCount == 2);
        u.readInt(); //array key

        ComponentData *d = ComponentData::getComponentById(root, QString::fromUtf8(u.readString()));
        if (paramCount == 2) {
            u.readInt(); //array key
            u.readObjectClassName();
            Select s(&u);
            if (!s.match(d, d->parent())) {
                d = 0;
            }
        }
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
            ComponentData *d = ComponentData::getComponentById(root, QString::fromUtf8(u.readString()));
            if (d) ret << d;
        }
        u.readArrayEnd();

        socket->write(serialize(ret));
    } else if (cmd == "getComponentById") {
        Q_ASSERT(paramCount == 1);
        u.readInt(); //array key

        ComponentData *d = ComponentData::getComponentById(root, QString::fromUtf8(u.readString()));
        socket->write(serialize(d));
    } else if (cmd == "getComponentsByDbId") {
        Q_ASSERT(paramCount == 2);
        u.readInt(); //array key

        QString id = QString::fromUtf8(u.readString());

        u.readInt(); //array key
        u.readObjectClassName();
        Select s(&u);
        qDebug() << s;
        
        QHash<QString, QByteArray> ret;
        foreach (ComponentData *d, s.filter(ComponentData::getComponentsByDbId(root, id), 0)) {
            ret[d->componentId()] = serialize(d);
        }
        socket->write(serialize(ret));
    } else if (cmd == "getComponentsBySameClasses") {
        Q_ASSERT(paramCount == 2);
        u.readInt(); //array key
        QList<QByteArray> classes = u.readString().split(',');

        u.readInt(); //array key
        u.readObjectClassName();
        Select s(&u);

        QList< ComponentData* > ret;
        int i=0;
        foreach (const QByteArray &c, classes) {
            foreach (ComponentData *d, ComponentData::getComponentsByClass(root, ComponentClass(QString::fromUtf8(c)))) {
                if (s.match(d, 0)) {
                    i++;
                    if (i > s.limitOffset) ret << d;
                    if (s.limitCount && ret.count() == s.limitCount) {
                        break;
                    }
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
        int i=0;
        foreach (const ComponentClass &c, classes) {
            foreach (ComponentData *d, ComponentData::getComponentsByClass(root, c)) {
                if (s.match(d, 0)) {
                    i++;
                    if (i > s.limitOffset) ret << d;
                    if (s.limitCount && ret.count() == s.limitCount) {
                        break;
                    }
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


        ComponentData *d = ComponentData::getComponentById(root, componentId);
        Q_ASSERT(d);

        debugVerbose( qDebug() << s; )
        
        QHash<QString, ComponentData*> ret;
        foreach (ComponentData *i, d->childComponents(s)) {
            qDebug() << "returning" << i->componentId();
            ret[i->componentId()] = i;
        }
        socket->write(serialize(ret));
    } else if (cmd == "countChildComponents") {
        Q_ASSERT(paramCount == 2);

        u.readInt(); //array key
        QString componentId(QString::fromUtf8(u.readString()));

        u.readInt(); //array key
        u.readObjectClassName();
        Select s(&u);


        ComponentData *d = ComponentData::getComponentById(root, componentId);
        Q_ASSERT(d);
        
        debug(
        foreach (ComponentData *c, d->childComponents(s)) {
            qDebug() << c->componentId();
        }
        )

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



        ComponentData *d = ComponentData::getComponentById(root, componentId);
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

        QList<IndexedString> ret;
        ret << root->componentClass().toIndexedString();
        foreach (Generator *g, Generator::generators(root)) {
            foreach (const ComponentClass &c, g->childComponentClasses()) {
                if (c.isEmpty()) continue;
                if (!ret.contains(c.toIndexedString())) {
                    ret << c.toIndexedString();
                }
                foreach (const IndexedString &p, c.plugins()) {
                    if (!ret.contains(p)) {
                        ret << p;
                    }
                }
            }
        }
        socket->write(serialize(ret));

    } else if (cmd == "getChildPageByPath") {
        Q_ASSERT(paramCount == 2);

        u.readInt(); //array key
        QString componentId(QString::fromUtf8(u.readString()));

        u.readInt(); //array key
        QString path = QString::fromUtf8(u.readString());

        ComponentData *d = ComponentData::getComponentById(root, componentId);
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
            for (int j=0; j<cntRows; ++j) {
                u.readInt(); //array key
                int cntFields = u.readArrayStart();
                IndexedString model;
                QString id;
                for (int k=0; k<cntFields; ++k) {
                    QString key = u.readString();
                    if (key == "model") {
                        model = IndexedString(u.readString());
                    } else if (key == "id") {
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
    } else if (cmd == "reset") {
        qDebug() << "RESET ======================================================================";
        Q_ASSERT(paramCount == 0);
        if (root) {
            qDebug() << root->componentId();
        } else {
            qDebug() << "no root yet; nothing to do";
        }
        if (root) {
            delete root;
            Generator::deleteGenerators(root);
        }
    } else {
        socket->write("ERROR: unknown command");
    }
    u.readArrayEnd();

    //cleanup
    if (ComponentData::m_uncachedDatas.values(QThread::currentThread()).count()) {
        qDebug() << "delete" << ComponentData::m_uncachedDatas.values(QThread::currentThread()).count() << "uncached datas";
        qDeleteAll(ComponentData::m_uncachedDatas.values(QThread::currentThread()));
    }
    ComponentData::m_uncachedDatas.remove(QThread::currentThread());
}
