#ifndef MODEL_H
#define MODEL_H

#include "IndexedString.h"
#include "Select.h"

class ComponentData;
class Select;
struct Generator;

class StandardModel;
class GeneratorModel;

class Model
{
public:
    static GeneratorModel *instance(Generator *generator);
    static StandardModel *instance(IndexedString phpModelClass);

    virtual ~Model() {}

    class Row
    {
    public:
        QVariant value(const IndexedString &field) const {
            return m_values[field];
        }
        QString id() const {
            return m_id;
        }

    private:
        friend class Model;
        Row(const QString id, const QHash<IndexedString, QVariant> &values) 
            : m_id(id), m_values(values) {}
        QString m_id;
        QHash<IndexedString, QVariant> m_values;
    };

    typedef QList<Row> RowSet;
    /*
    class RowSet
    {
    public:
        bool hasNext();
        QVariant value(const IndexedString &field);
        Row row();
        QList<Row> toList();
    };
    */

    RowSet fetchRows(const QHash<IndexedString, IndexedString> &fields, const Select &select, QList<QByteArray> args = QList<QByteArray>()) const;

    Row fetchRow(const QHash<IndexedString, IndexedString> &fields, const Select &select) const
    {
        Select s(select);
        s.limitCount = 1;
        return fetchRows(fields, s).first();
    }

    Row fetchRow(const QHash<IndexedString, IndexedString> &fields, const QString &primaryId) const
    {
        Select s;
        if (!primaryId.isEmpty()) {
            s.where << new SelectExprWhereEquals(IndexedString("id"), primaryId);
        }
        return fetchRow(fields, s);
    }

    RowSet fetchRows(const QHash<IndexedString, IndexedString> &fields, const QString &primaryId) const
    {
        Select s;
        if (!primaryId.isEmpty()) {
            s.where << new SelectExprWhereEquals(IndexedString("id"), primaryId);
        }
        return fetchRows(fields, s);
    }
protected:
    virtual QList<QByteArray> _args(const Select &select, QList<QByteArray> args) const = 0;
    Row createRow(const QString &id, const QHash<IndexedString, QVariant> &values) const {
        return Row(id, values);
    }

private:
    friend class Generator;
    static QHash<IndexedString, StandardModel*> m_instances;
    static QHash<Generator *, GeneratorModel*> m_instancesGenerator;
};

class StandardModel : public Model
{
public:

protected:
    virtual QList<QByteArray> _args(const Select& select, QList<QByteArray> args) const;
private:
    friend class Model;
    StandardModel(const IndexedString &c) : m_modelClass(c) {}
    IndexedString m_modelClass;
};

class GeneratorModel : public Model
{
public:
    RowSet fetchRows(QHash<IndexedString, IndexedString> fields, const ComponentData *parentData, const Select &select) const;
    RowSet fetchRows(const QHash<IndexedString, IndexedString> &fields, const ComponentData *parentData, const QString &primaryId) const
    {
        Select s;
        if (!primaryId.isEmpty()) {
            s.where << new SelectExprWhereEquals(IndexedString("id"), primaryId);
        }
        return fetchRows(fields, parentData, s);
    }

protected:
    virtual QList<QByteArray> _args(const Select& select, QList<QByteArray> args) const;
private:
    friend class Model;
    GeneratorModel(Generator *g) : m_generator(g) {}
    Generator *m_generator;
};

/*
class Model
{
public:
    static QHash<QString, QHash<IndexedString, QVariant> > rowData(IndexedString model, QHash<IndexedString, IndexedString> fields, const QString& onlyId = QString());
    static QHash<QString, QVariant> rowData(IndexedString model, IndexedString field, const QString& onlyId = QString());

    //verwendet im moment fix die nameColumn
    //TODO: wenn benötigt sollte das auch mit anderen columns gehen
    static QHash<QString, QHash<IndexedString, QVariant> > rowData(Generator *g, QHash<IndexedString, IndexedString> fields, const QString& onlyId = QString());
    static QHash<QString, QVariant> rowData(Generator *g, IndexedString field, const QString& onlyId = QString());
private:
    static QHash<QString, QHash<IndexedString, QVariant> > _rowData(QList<QByteArray> args, QList<IndexedString> fields);
};
*/

#endif // MODEL_H
