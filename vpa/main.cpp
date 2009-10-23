
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
#include "ConnectionServer.h"

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

int main(int argc, char** argv)
{
    QCoreApplication app(argc, argv);

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


    ConnectionServer server;
    if (!server.listen(QHostAddress::Any, 1234)) {
        qFatal(server.errorString().toAscii().constData());
    }
    /*
    forever {
        if (server.waitForNewConnection(30000)) {
        }
    }
    */
    return app.exec();
}

