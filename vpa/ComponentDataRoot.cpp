#include <QTime>

#include "ComponentDataRoot.h"
#include "PhpProcess.h"

QHash<IndexedString, ComponentDataRoot*> ComponentDataRoot::m_instances;



ComponentDataRoot::~ComponentDataRoot()
{
    m_instances.remove(componentClass().toIndexedString());
    m_idHash.remove(this);
}


void ComponentDataRoot::initInstance(IndexedString componentClass)
{
    qDebug() << "****** initializing RootComponent" << componentClass;
    {
        qDebug() << "loading componentClasses";
        QTime stopWatch;
        stopWatch.start();
        ComponentClass::init(componentClass);
        qDebug() << stopWatch.elapsed() << "ms";
    }

    ComponentDataRoot *root = new ComponentDataRoot(componentClass);
    m_instances[componentClass] = root;

    {
        qDebug() << "creating generators";
        QTime stopWatch;
        stopWatch.start();
        Generator::createGenerators(root);
        qDebug() << "generators" << stopWatch.elapsed() << "ms";
    }

    {
        qDebug() << "build root components";
        QTime stopWatch;
        int startDatas = ComponentData::count;
        stopWatch.start();
        BuildOnlyPagesGeneratorStrategy s;
        Generator::buildWithGenerators(root, &s);
        qDebug() << "root components" << stopWatch.elapsed() << "ms" << (ComponentData::count-startDatas) << "datas";
    }

    {
        qDebug() << "build dbIdShortcut components";
        QTime stopWatch;
        int startDatas = ComponentData::count;
        stopWatch.start();
        BuildWithDbIdShortcutStrategy s;
        Generator::buildWithGenerators(root, &s);
        qDebug() << "dbIdShortcut components" << stopWatch.elapsed() << "ms" << (ComponentData::count-startDatas) << "datas";
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
    
    /*
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

    qDebug() << "php memory usage" << PhpProcess::getInstance()->call(0, "memory-usage");
    qDebug() << "";
    */
}
