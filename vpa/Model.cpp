#include "Model.h"

#include <QSqlQuery>
#include <QSqlError>
#include <QBuffer>
#include <QTime>

#include "PhpProcess.h"
#include "Unserializer.h"
#include "Generator.h"

QHash<QString, QVariant> Model::rowData(Generator *g)
{
    QList<QByteArray> args;
    args << QByteArray("--generator-class=") + g->componentClass.toString().toUtf8();
    args << QByteArray("--generator-key=") + g->key.toString().toUtf8();
    args << QByteArray("--generator-name-field=1");
    return _rowData(args);
}

QHash<QString, QVariant> Model::rowData(IndexedString model, IndexedString field, const QString& onlyId)
{
    Q_ASSERT(model != IndexedString("Vps_Model_FnF"));
    Q_ASSERT(model != IndexedString("Vps_Model_FnFFile"));
    QList<QByteArray> args;
    args << QByteArray("--field=") + field.toString().toUtf8();
    args << QByteArray("--model=") + model.toString().toUtf8();
    if (!onlyId.isEmpty()) {
        args << QByteArray("--id=") + onlyId.toUtf8();
    }
    return _rowData(args);
}

QHash<QString, QVariant> Model::_rowData(QList<QByteArray> args)
{
    QHash<QString, QVariant> ret;

    QTime stopWatch;
    stopWatch.start();
    qDebug() << "model-get-rows" << args;
    QByteArray data = PhpProcess::getInstance()->call(0, "model-get-rows", args);
    qDebug() << stopWatch.elapsed() << "ms";

    QBuffer buffer(&data);
    buffer.open(QIODevice::ReadOnly);
    Unserializer u(&buffer);

    if (u.device()->peek(2) == "N;") {
        //feld gibts ned
    } else if (u.device()->peek(2) == "s:") {
        qDebug() << "sql (good)";
        //wir haben einen sql string bekommen
        QString sql = QString::fromUtf8(u.readString());
        qDebug() << sql;
        QSqlQuery query;
        if (!query.exec(sql)) {
            qCritical() << "can't execute query GeneratorWithModel::rowData" << query.lastError() << sql;
            Q_ASSERT(0);
            return ret;
        }
        while (query.next()) {
            QString id = query.value(0).toString();
            Q_ASSERT(!id.isEmpty());
            ret[id] = query.value(1);
        }
    } else {
        qDebug() << "rows (bad)";
        //wir haben gleich die daten bekommen
        int rowCount = u.readArrayStart();
        for (int i=0; i < rowCount; ++i) {
            QVariant id = u.readVariant();
            Q_ASSERT(!id.toString().isEmpty());
            ret[id.toString()] =  u.readVariant();
        }
        u.readArrayEnd();
    }
    qDebug() << ret.count() << "rows fetched";
    return ret;
}