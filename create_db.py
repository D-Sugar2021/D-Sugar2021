import uno
import unohelper
from com.sun.star.sdb import XDocumentDataSource
from com.sun.star.sdbc import XConnection

def create_database():
    # Get the UNO component context
    local_context = uno.getComponentContext()
    resolver = local_context.ServiceManager.createInstanceWithContext(
        "com.sun.star.bridge.UnoUrlResolver", local_context
    )
    context = resolver.resolve("uno:socket,host=localhost,port=2002;urp;StarOffice.ComponentContext")
    service_manager = context.ServiceManager

    # Create a new database document
    db_context = service_manager.createInstanceWithContext("com.sun.star.sdb.DatabaseContext", context)
    db_document = db_context.createInstance()

    # Set the database to use HSQLDB embedded
    data_source = db_document.DataSource
    data_source.URL = "sdbc:embedded:hsqldb"

    # Save the database document
    db_document.storeAsURL("file:///app/exam.odb", [])

    # Get a connection to the database
    connection = data_source.getConnection("", "")
    statement = connection.createStatement()

    # Read the schema from the SQL file
    with open("schema.sql", "r") as f:
        schema = f.read()

    # Execute the schema
    statement.execute(schema)

    # Close the connection
    connection.close()
    db_document.close(True)

if __name__ == "__main__":
    create_database()
