
#include <QtSql>
#include <QProcess>
#include <QList>
#include <QtNetwork/QLocalServer>
#include <QtNetwork/QLocalSocket>
#include <QXmlStreamReader>

#include "ComponentDataRoot.h"
#include "PhpProcess.h"
#include "ComponentClass.h"
#include "Unserializer.h"
#include "Select.h"
#include "Generator.h"
#include "ConnectionServer.h"

int main(int argc, char** argv)
{
    QCoreApplication app(argc, argv);

    if (argc < 2) {
        qFatal("First parameter needs to be path to the web");
    }
    QDir webPath(argv[1]);

    QFile pidFile(webPath.absolutePath()+"/application/temp/vpa.pid");
    pidFile.open(QIODevice::WriteOnly);
    qDebug() << QCoreApplication::applicationPid();
    pidFile.write(QByteArray::number(QCoreApplication::applicationPid()));
    pidFile.close();

    QTime startupStopWatch;
    startupStopWatch.start();
    {
        qDebug() << "start php process";
        QTime stopWatch;
        stopWatch.start();
        PhpProcess::setup(webPath.absolutePath());
        qDebug() << stopWatch.elapsed() << "ms";
    }

    {
        qDebug() << "connect to database";
        QTime stopWatch;
        stopWatch.start();
        PhpProcess *p = PhpProcess::getInstance();
        QXmlStreamReader xmlDb(p->call(0, "dbconfig"));
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
    
    //ComponentDataRoot::getInstance(IndexedString(PhpProcess::getInstance()->call(0, "root-component")));

    qDebug() << "startup time" << startupStopWatch.elapsed() << "ms";

    ConnectionServer server;

    QString socketPath = webPath.absolutePath()+"/application/temp/vpa.sock";
    qDebug() << "opening socket" << socketPath;
    if (!server.listen(socketPath)) {
        qFatal(server.errorString().toAscii().constData());
    }

    return app.exec();
}

