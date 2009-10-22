
#include <QtSql>
#include <QProcess>
#include <QList>
#include <QtNetwork/QTcpServer>
#include <QtNetwork/QTcpSocket>

#include "ComponentDataRoot.h"
#include "PhpProcess.h"
#include "ComponentClass.h"
#include "Unserializer.h"
#include "Select.h"
#include "Generator.h"

void createGenerators()
{
    PhpProcess *p = PhpProcess::getInstance();
    QXmlStreamReader xml(p->call("generators"));
    while (!xml.atEnd()) {
        xml.readNext();
        if (xml.isStartElement() && xml.name() == "generator") {
            Generator::Type type = Generator::Unknown;
            Generator *g;
            QString t = xml.attributes().value("type").toString();
            if (t == "static") {
                type = Generator::Static;
                g = new GeneratorStatic;
            } else if (t == "table") {
                type = Generator::Table;
                g = new GeneratorTable;
            } else if (t == "tableSql") {
                type = Generator::TableSql;
                g = new GeneratorTableSql;
            } else if (t == "load") {
                type = Generator::Load;
                g = new GeneratorLoad;
            } else if (t == "pages") {
                type = Generator::Pages;
                g = new GeneratorPages;
            } else if (t == "tableSqlWithComponent") {
                type = Generator::TableSqlWithComponent;
                g = new GeneratorTableSqlWithComponent;
            } else if (t == "loadSql") {
                type = Generator::LoadSql;
                g = new GeneratorLoadSql;
            } else if (t == "loadSqlWithComponent") {
                type = Generator::LoadSqlWithComponent;
                g = new GeneratorLoadSqlWithComponent;
            } else if (t == "linkTag") {
                type = Generator::LinkTag;
                g = new GeneratorLinkTag;
            } else {
                Q_ASSERT(0);
            }
            g->componentClass = ComponentClass(xml.attributes().value("componentClass").toString());

            ComponentClass component;
            QHash<IndexedString, ComponentClass> components;
            QList<GeneratorTable::Row> rows;
            QString sql;
            QString tableName;
            bool whereComponentId = false;
            while (!xml.atEnd()) {
                if (xml.isStartElement() && xml.name() == "key") {
                    g->key = IndexedString(xml.readElementText());
                }
                if (xml.isStartElement() && xml.name() == "class") {
                    g->generatorClass = IndexedString(xml.readElementText());
                }
                if (xml.isStartElement() && xml.name() == "parentClass") {
                    g->parentClasses << IndexedString(xml.readElementText());
                }
                if (xml.isStartElement() && xml.name() == "box") {
                    g->box = IndexedString(xml.readElementText());
                }
                if (xml.isStartElement() && xml.name() == "model") {
                    g->model = IndexedString(xml.readElementText());
                }
                if (xml.isStartElement() && xml.name() == "component") {
                    if (!xml.attributes().value("key").isEmpty()) {
                        Q_ASSERT(component.isEmpty());
                        IndexedString k = IndexedString(xml.attributes().value("key").toString());
                        QString c = xml.readElementText();
                        Q_ASSERT(!c.isEmpty());
                        components[k] = ComponentClass(c);
                    } else {
                        Q_ASSERT(components.isEmpty());
                        QString c = xml.readElementText();
                        Q_ASSERT(!c.isEmpty());
                        component = ComponentClass(c);
                    }
                }
                if (xml.isStartElement() && xml.name() == "idSeparator") {
                    QString s = xml.readElementText();
                    if (s == QString('-')) {
                        g->idSeparator = Generator::Dash;
                    } else if (s == QString('_')) {
                        g->idSeparator = Generator::Underscore;
                    }
                }
                if (xml.isStartElement() && xml.name() == "sql") {
                    sql = xml.readElementText();
                }
                if (xml.isStartElement() && xml.name() == "tableName") {
                    tableName = xml.readElementText();
                }
                if (xml.isStartElement() && xml.name() == "whereComponentId") {
                    whereComponentId = (bool)xml.readElementText().toInt();
                }
                if (xml.isStartElement() && xml.name() == "pageGenerator") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->componentTypes |= Generator::TypePage;
                    }
                }
                if (xml.isStartElement() && xml.name() == "boxGenerator") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->componentTypes |= Generator::TypeBox;
                    }
                }
                if (xml.isStartElement() && xml.name() == "multiBoxGenerator") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->componentTypes |= Generator::TypeMultiBox;
                    }
                }
                if (xml.isStartElement() && xml.name() == "pseudoPageGenerator") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->componentTypes |= Generator::TypePseudoPage;
                    }
                }
                if (xml.isStartElement() && xml.name() == "pagesGenerator") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->componentTypes |= Generator::TypePagesGenerator;
                    }
                }
                if (xml.isStartElement() && xml.name() == "inherit") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->componentTypes |= Generator::TypeInherit;
                    }
                }
                if (xml.isStartElement() && xml.name() == "unique") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->componentTypes |= Generator::TypeUnique;
                    }
                }
                if (xml.isStartElement() && xml.name() == "inherits") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->componentTypes |= Generator::TypeInherits;
                    }
                }
                if (xml.isStartElement() && xml.name() == "showInMenu") {
                    if ((bool)xml.readElementText().toInt()) {
                        g->componentTypes |= Generator::TypeShowInMenu;
                    }
                }

                if (xml.isStartElement() && xml.name() == "dbIdShortcut") {
                    g->dbIdPrefix = IndexedString(xml.readElementText());
                }
                if (type == Generator::Static) {
                    if (xml.isStartElement() && xml.name() == "filename") {
                        static_cast<GeneratorStatic*>(g)->filename = xml.readElementText();
                    }
                    if (xml.isStartElement() && xml.name() == "name") {
                        static_cast<GeneratorStatic*>(g)->name = xml.readElementText();
                    }
                }
                if (xml.isStartElement() && xml.name() == "rows") {
                    while (!xml.atEnd()) {
                        if (xml.isStartElement() && xml.name() == "row") {
                            QString id = xml.attributes().value("id").toString();
                            QString name = xml.readElementText();
                            rows << GeneratorTable::Row(id, name);
                        }
                        if (xml.isEndElement() && xml.name() == "rows") {
                            break;
                        }
                        xml.readNext();
                    }
                }
                if (xml.isEndElement() && xml.name() == "generator") {
                    break;
                }
                xml.readNext();
            }
            if (type == Generator::Static) {
                if (!component.isEmpty()) components[g->key] = component;
                static_cast<GeneratorStatic*>(g)->component = components;
            } else if (type == Generator::Table) {
                static_cast<GeneratorTable*>(g)->rows = rows;
                static_cast<GeneratorTable*>(g)->component = component;
            } else if (type == Generator::TableSql) {
                static_cast<GeneratorTableSql*>(g)->tableName = tableName;
                static_cast<GeneratorTableSql*>(g)->whereComponentId = whereComponentId;
                static_cast<GeneratorTableSql*>(g)->component = component;
            } else if (type == Generator::TableSqlWithComponent) {
                static_cast<GeneratorTableSqlWithComponent*>(g)->tableName = tableName;
                static_cast<GeneratorTableSqlWithComponent*>(g)->whereComponentId = whereComponentId;
                Q_ASSERT(!components.isEmpty());
                static_cast<GeneratorTableSqlWithComponent*>(g)->component = components;
            } else if (type == Generator::Load) {
                if (!component.isEmpty()) components[g->key] = component;
                static_cast<GeneratorLoad*>(g)->component = components;
            } else if (type == Generator::Pages) {
                if (!component.isEmpty()) components[g->key] = component;
                static_cast<GeneratorLoad*>(g)->component = components;
            } else if (type == Generator::LoadSql) {
                Q_ASSERT(!component.isEmpty());
                static_cast<GeneratorLoadSql*>(g)->component = component;
            } else if (type == Generator::LoadSqlWithComponent) {
                Q_ASSERT(!components.isEmpty());
                static_cast<GeneratorLoadSqlWithComponent*>(g)->component = components;
            } else if (type == Generator::LinkTag) {
                Q_ASSERT(!components.isEmpty());
                static_cast<GeneratorLinkTag*>(g)->component = components;
            } else {
                continue;
            }

            g->preload();
        }
    }
    if (xml.hasError()) {
        qDebug() << "error reading generators";
        qFatal(xml.errorString().toAscii().data());
    }
}


void dispatchCommand(const QByteArray &cmd, QByteArray args, QTcpSocket *socket)
{
    QByteArray prettyArgs = args;
    prettyArgs.replace('\0', "\\0");
    qDebug() << cmd << prettyArgs;

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
            qDebug() << "invalid";
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
        Q_ASSERT(paramCount == 1);
        u.readInt(); //array key

        QList< ComponentData* > d = ComponentData::getComponentsByDbId(QString::fromUtf8(u.readString()));
        socket->write(serialize(d));
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
                if (s.match(d)) {
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

        QList<ComponentData*> ret;
        foreach (const ComponentClass &c, classes) {
            foreach (ComponentData *d, ComponentData::getComponentsByClass(c)) {
                if (s.match(d)) {
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

        qDebug() << s;

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
        qDebug() << "select" << s;


        u.readInt(); //array key
        u.readObjectClassName();
        Select childSelect(&u);
        qDebug() << "childSelect" << childSelect;

        

        ComponentData *d = ComponentData::getComponentById(componentId);
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

    } else {
        socket->write("ERROR: unknown command");
    }
    u.readArrayEnd();
}

int main(int argc, char** argv)
{
    if (argc < 2) {
        qFatal("First parameter needs to be path to the web");
    }

    QTime startupStopWatch;
    startupStopWatch.start();
    {
        qDebug() << "start php process";
        QTime stopWatch;
        stopWatch.start();
        PhpProcess::setup(argv[1]);
        qDebug() << stopWatch.elapsed() << "ms";
    }

    {
        qDebug() << "connect to database";
        QTime stopWatch;
        stopWatch.start();
        PhpProcess *p = PhpProcess::getInstance();
        QXmlStreamReader xmlDb(p->call("dbconfig"));
        QSqlDatabase db = QSqlDatabase::addDatabase("QMYSQL");
        while (!xmlDb.atEnd()) {
            xmlDb.readNext();
            if (xmlDb.isStartElement() && xmlDb.name() == "host") {
                db.setHostName(xmlDb.readElementText());
            }
            if (xmlDb.isStartElement() && xmlDb.name() == "username") {
                db.setUserName(xmlDb.readElementText());
            }
            if (xmlDb.isStartElement() && xmlDb.name() == "password") {
                db.setPassword(xmlDb.readElementText());
            }
            if (xmlDb.isStartElement() && xmlDb.name() == "dbname") {
                db.setDatabaseName(xmlDb.readElementText());
            }
        }
        if (!db.open()) {
            qCritical() << db.lastError();
            qFatal("can't open db");
        }
        qDebug() << stopWatch.elapsed() << "ms";
    }

    {
        qDebug() << "loading componentClasses";
        QTime stopWatch;
        stopWatch.start();
        ComponentClass::init();
        qDebug() << stopWatch.elapsed() << "ms";
    }

    {
        qDebug() << "creating generators";
        QTime stopWatch;
        stopWatch.start();
        createGenerators();
        qDebug() << stopWatch.elapsed() << "ms";
    }


    ComponentDataRoot::initInstance(IndexedString(PhpProcess::getInstance()->call("root-component")));
    ComponentDataRoot *root = ComponentDataRoot::getInstance();
    {
        qDebug() << "build root components";
        QTime stopWatch;
        int startDatas = ComponentData::count;
        stopWatch.start();
        BuildOnlyRootStrategy s;
        Generator::buildWithGenerators(root, &s);
        qDebug() << stopWatch.elapsed() << "ms" << (ComponentData::count-startDatas) << "datas";
    }

    {
        qDebug() << "build dbIdShortcut components";
        QTime stopWatch;
        int startDatas = ComponentData::count;
        stopWatch.start();
        BuildWithDbIdShortcutStrategy s;
        Generator::buildWithGenerators(root, &s);
        qDebug() << stopWatch.elapsed() << "ms" << (ComponentData::count-startDatas) << "datas";
    }
    /*
    {
        qDebug() << "build component tree";
        QTime stopWatch;
        stopWatch.start();
        Generator::buildWithGenerators(root);
        qDebug() << (stopWatch.elapsed() / 1000) << "s";
    }
    */
    
    qDebug() << "";
    qDebug() << ComponentData::count << "datas created";
    qDebug() << "";
    qDebug() << "Generators used:";
    qDebug() << "Static" << Generator::buildCallCount[Generator::Static];
    qDebug() << "Load" << Generator::buildCallCount[Generator::Load];
    qDebug() << "Pages" << Generator::buildCallCount[Generator::Pages];
    qDebug() << "Table" << Generator::buildCallCount[Generator::Table];
    qDebug() << "TableSql" << Generator::buildCallCount[Generator::TableSql];
    qDebug() << "TableSqlWithComponent" << Generator::buildCallCount[Generator::TableSqlWithComponent];
    qDebug() << "LoadSql" << Generator::buildCallCount[Generator::LoadSql];
    qDebug() << "LoadSqlWithComponent" << Generator::buildCallCount[Generator::LoadSqlWithComponent];
    qDebug() << "LinkTag" << Generator::buildCallCount[Generator::LinkTag];
    qDebug() << "";

    qDebug() << "php memory usage" << PhpProcess::getInstance()->call("memory-usage");
    qDebug() << "";
    qDebug() << "startup time" << startupStopWatch.elapsed() << "ms";



    QTcpServer server;
    if (!server.listen(QHostAddress::Any, 1234)) {
        qFatal(server.errorString().toAscii().constData());
    }
    forever {
        if (server.waitForNewConnection(30000)) {
            QTcpSocket *socket = server.nextPendingConnection();
            qDebug() << "new connection" << socket;
            socket->waitForConnected();
            forever {
                QByteArray cmd;
                do {
                    if (socket->state() == QAbstractSocket::UnconnectedState) {
                        break;
                    }
                    if (socket->waitForReadyRead()) {
                        cmd.append(socket->readAll());
                    }
                } while(!cmd.endsWith('\0'));
                if (socket->state() == QAbstractSocket::UnconnectedState) {
                    break;
                }
                QTime stopWatch;
                stopWatch.start();
                cmd.chop(1);
                QByteArray args;
                if (cmd.indexOf(' ') != -1) {
                    args = cmd.mid(cmd.indexOf(' ')+1);
                    cmd = cmd.left(cmd.indexOf(' '));
                }
                dispatchCommand(cmd, args, socket);
                socket->write("\0\n", 2);
                socket->flush();
                socket->waitForBytesWritten();
                qDebug() << stopWatch.elapsed() << "ms" << ComponentData::count << "datas";
//                 qDebug() << "php memory usage" << PhpProcess::getInstance()->call("memory-usage");
//                 qDebug() << "";
            }
        }
    }
    return 0;
}

