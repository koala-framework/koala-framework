#ifndef MODEL_H
#define MODEL_H

#include "IndexedString.h"

struct Generator;
class Model
{
public:
    static QHash<QString, QVariant> rowData(IndexedString model, IndexedString field, const QString& onlyId = QString());
    
    //verwendet im moment fix die nameColumn
    //TODO: wenn benötigt sollte das auch mit anderen columns gehen
    static QHash<QString, QVariant> rowData(Generator *g);
private:
    static QHash<QString, QVariant> _rowData(QList<QByteArray> args);
};

#endif // MODEL_H
