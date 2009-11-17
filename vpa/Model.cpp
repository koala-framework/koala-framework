#include "Model.h"

#include <QSqlQuery>
#include <QSqlError>
#include <QBuffer>
#include <QTime>

#include "PhpProcess.h"
#include "Unserializer.h"
#include "Generator.h"

QHash<QString, QHash<IndexedString, QVariant> > Model::rowData(Generator *g, QHash<IndexedString, IndexedString> fields)
{
    QList<QByteArray> args;
    args << QByteArray("--generator-class=") + g->componentClass.toString().toUtf8();
    args << QByteArray("--generator-key=") + g->key.toString().toUtf8();
    QByteArray arg;
    QList<IndexedString> fieldNames;
    QHashIterator<IndexedString, IndexedString> i(fields);
    while (i.hasNext()) {
        i.next();
        if (!arg.isEmpty()) arg += ",";
        arg += i.value().toString().toUtf8();
        fieldNames << i.key();
    }
    args << QByteArray("--fields=") + arg;
    return _rowData(args, fieldNames);
}

QHash<QString, QHash<IndexedString, QVariant> > Model::rowData(IndexedString model, QHash<IndexedString, IndexedString> fields, const QString& onlyId)
{
    Q_ASSERT(model != IndexedString("Vps_Model_FnF"));
    Q_ASSERT(model != IndexedString("Vps_Model_FnFFile"));
    QList<QByteArray> args;
    QByteArray arg;
    QList<IndexedString> fieldNames;
    QHashIterator<IndexedString, IndexedString> i(fields);
    while (i.hasNext()) {
        i.next();
        if (!arg.isEmpty()) arg += ",";
        arg += i.value().toString().toUtf8();
        fieldNames << i.key();
    }
    args << QByteArray("--fields=") + arg;
    args << QByteArray("--model=") + model.toString().toUtf8();
    if (!onlyId.isEmpty()) {
        args << QByteArray("--id=") + onlyId.toUtf8();
    }
    return _rowData(args, fieldNames);
}


QHash<QString, QVariant> Model::rowData(IndexedString model, IndexedString field, const QString& onlyId)
{
    QHash<IndexedString, IndexedString> fields;
    fields[IndexedString("field")] = field;
    QHash<QString, QHash<IndexedString, QVariant> > rows = rowData(model, fields, onlyId);
    QHashIterator<QString, QHash<IndexedString, QVariant> > i(rows);
    QHash<QString, QVariant> ret;
    while (i.hasNext()) {
        i.next();
        ret[i.key()] = i.value()[IndexedString("field")];
    }
    return ret;
}


QHash<QString, QVariant> Model::rowData(Generator* g, IndexedString field)
{
    QHash<QString, QVariant> ret;

    QHash<IndexedString, IndexedString> fields;
    fields[IndexedString("field")] = field;
    QHash<QString, QHash<IndexedString, QVariant> > rows = rowData(g, fields);
    QHashIterator<QString, QHash<IndexedString, QVariant> > i(rows);
    while (i.hasNext()) {
        i.next();
        ret[i.key()] = i.value()[IndexedString("field")];
    }
    return ret;
}



QHash<QString, QHash<IndexedString, QVariant> > Model::_rowData(QList<QByteArray> args, QList<IndexedString> fields)
{
    QHash<QString, QHash<IndexedString, QVariant> > ret;

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
            for (int j=0; j < fields.count(); ++j) {
                ret[id][fields[j]] = query.value(j+1);
            }
        }
    } else {
        qDebug() << "rows (bad)";
        //wir haben gleich die daten bekommen
        int rowCount = u.readArrayStart();
        for (int i=0; i < rowCount; ++i) {
            QString id = u.readVariant().toString();
            Q_ASSERT(!id.isEmpty());
            int colCount = u.readArrayStart();
            Q_ASSERT(colCount == fields.count());
            for (int j=0; j < colCount; ++j) {
                u.readInt(); //key
                ret[id][fields[j]] = u.readVariant();
            }
            u.readArrayEnd();
        }
        u.readArrayEnd();
    }
    qDebug() << ret.count() << "rows fetched";
    return ret;
}