#ifndef MODEL_H
#define MODEL_H

#include "IndexedString.h"

struct Generator;
class Model
{
public:
    static QHash<QString, QHash<IndexedString, QVariant> > rowData(IndexedString model, QHash<IndexedString, IndexedString> fields, const QString& onlyId = QString());
    static QHash<QString, QVariant> rowData(IndexedString model, IndexedString field, const QString& onlyId = QString());

    //verwendet im moment fix die nameColumn
    //TODO: wenn benötigt sollte das auch mit anderen columns gehen
    static QHash<QString, QHash<IndexedString, QVariant> > rowData(Generator *g, QHash<IndexedString, IndexedString> fields);
    static QHash<QString, QVariant> rowData(Generator *g, IndexedString field);
private:
    static QHash<QString, QHash<IndexedString, QVariant> > _rowData(QList<QByteArray> args, QList<IndexedString> fields);
};

#endif // MODEL_H
