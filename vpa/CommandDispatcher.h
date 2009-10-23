#ifndef COMMANDDISPATCHER_H
#define COMMANDDISPATCHER_H
#include <QByteArray>

class QIODevice;
class CommandDispatcher
{
public:
    static void dispatchCommand(const QByteArray &cmd, QByteArray args, QIODevice *socket);
};

#endif
