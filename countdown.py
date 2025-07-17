import uno
import unohelper
from com.sun.star.awt import XActionListener

class CountdownTimer(unohelper.Base, XActionListener):
    def __init__(self, ctx, dialog, label):
        self.ctx = ctx
        self.dialog = dialog
        self.label = label
        self.seconds = 60 * 60 # 1 hour

    def start(self):
        self.timer = uno.createUnoStruct("com.sun.star.awt.Time")
        self.timer.Hours = 1
        self.timer.Minutes = 0
        self.timer.Seconds = 0

        self.label.setText(str(self.timer))

        # Create a timer that fires every second
        timer = self.ctx.ServiceManager.createInstanceWithContext("com.sun.star.awt.Timer", self.ctx)
        timer.addActionListener(self)
        timer.start(1000, 0)

    def actionPerformed(self, event):
        self.seconds -= 1

        hours = self.seconds // 3600
        minutes = (self.seconds % 3600) // 60
        seconds = self.seconds % 60

        self.timer.Hours = hours
        self.timer.Minutes = minutes
        self.timer.Seconds = seconds

        self.label.setText(str(self.timer))

        if self.seconds <= 0:
            # Time's up!
            self.dialog.endExecute()

def start_countdown(event):
    dialog = event.Source.Model.Parent
    label = dialog.getControl("TimerLabel")

    countdown = CountdownTimer(uno.getComponentContext(), dialog, label)
    countdown.start()
