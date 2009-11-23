#include "Model.h"

#include <QSqlQuery>
#include <QSqlError>
#include <QBuffer>
#include <QTime>

#include "PhpProcess.h"
#include "Unserializer.h"
#include "Generator.h"
#include "ComponentData.h"

QHash<IndexedString, StandardModel*> Model::m_instances;
QHash<Generator *, GeneratorModel*> Model::m_instancesGenerator;

Model::RowSet Model::fetchRows(const QHash<IndexedString, IndexedString>& fields, const Select& select, QList<QByteArray> args)  const
{
    Model::RowSet ret;

    QTime stopWatch;
    stopWatch.start();

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
    args << QByteArray("--select=") + serialize(select).replace('\0', "\\0");

    args = _args(select, args);
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
            QHash<IndexedString, QVariant> values;
            for (int j=0; j < fieldNames.count(); ++j) {
                values[fieldNames[j]] = query.value(j+1);
            }
            ret << createRow(id, values);
        }
    } else {
        qDebug() << "rows (bad)";
        //wir haben gleich die daten bekommen
        int rowCount = u.readArrayStart();
        for (int i=0; i < rowCount; ++i) {
            QString id = u.readVariant().toString();
            Q_ASSERT(!id.isEmpty());
            int colCount = u.readArrayStart();
            Q_ASSERT(colCount == fieldNames.count());
            QHash<IndexedString, QVariant> values;
            for (int j=0; j < colCount; ++j) {
                u.readInt(); //key
                values[fieldNames[j]] = u.readVariant();
            }
            ret << createRow(id, values);
            u.readArrayEnd();
        }
        u.readArrayEnd();
    }
    qDebug() << ret.count() << "rows fetched";
    return ret;
}



QList<QByteArray> GeneratorModel::_args(const Select& select, QList<QByteArray> args) const
{
    Q_UNUSED(select);
    args << QByteArray("--generator-class=") + m_generator->componentClass.toString().toUtf8();
    args << QByteArray("--generator-key=") + m_generator->key.toString().toUtf8();
//     if (!onlyId.isEmpty()) {
//         args << QByteArray("--id=") + onlyId.toUtf8();
//     }
    return args;
}

QList<QByteArray> StandardModel::_args(const Select &select, QList<QByteArray> args) const
{
    Q_UNUSED(select);
    args << QByteArray("--model=") + m_modelClass.toString().toUtf8();
//     if (!onlyId.isEmpty()) {
//         args << QByteArray("--id=") + onlyId.toUtf8();
//     }
    return args;
}

Model::RowSet GeneratorModel::fetchRows(QHash<IndexedString, IndexedString> fields, const ComponentData* parentData, const Select &select) const
{
    QList<QByteArray> args;
    //args << QByteArray("--parent-component-id=") + parentData->componentId().toUtf8();
    args << QByteArray("--parent-data=") + serialize(parentData).replace('\0', "\\0");
    return Model::fetchRows(fields, select, args);
}

GeneratorModel* Model::instance(Generator* generator)
{
    if (!m_instancesGenerator.contains(generator)) {
        m_instancesGenerator[generator] = new GeneratorModel(generator);
    }
    return m_instancesGenerator[generator];
}

StandardModel* Model::instance(IndexedString phpModelClass)
{
    Q_ASSERT(phpModelClass != IndexedString("Vps_Model_FnF"));
    Q_ASSERT(phpModelClass != IndexedString("Vps_Model_FnFFile"));
    if (!m_instances.contains(phpModelClass)) {
        m_instances[phpModelClass] = new StandardModel(phpModelClass);
    }
    return m_instances[phpModelClass];
}
