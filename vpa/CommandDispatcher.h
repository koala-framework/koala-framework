#ifndef COMMANDDISPATCHER_H
#define COMMANDDISPATCHER_H
#include <QByteArray>

class ComponentDataRoot;
class QIODevice;
class CommandDispatcher
{
public:
    static void dispatchCommand(const ComponentDataRoot *root, const QByteArray &cmd, QByteArray args, QIODevice *socket);
};

#endif
