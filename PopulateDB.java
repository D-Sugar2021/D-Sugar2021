import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.io.FileReader;
import com.opencsv.CSVReader;

public class PopulateDB {
    public static void main(String[] args) {
        Connection c = null;
        try {
            Class.forName("org.hsqldb.jdbc.JDBCDriver");
            c = DriverManager.getConnection("jdbc:hsqldb:file:exam", "SA", "");

            // Populate students
            CSVReader reader = new CSVReader(new FileReader("students.csv"));
            String[] nextLine;
            reader.readNext(); // Skip header
            while ((nextLine = reader.readNext()) != null) {
                PreparedStatement ps = c.prepareStatement("INSERT INTO students (student_id, first_name, last_name) VALUES (?, ?, ?)");
                ps.setInt(1, Integer.parseInt(nextLine[0]));
                ps.setString(2, nextLine[1]);
                ps.setString(3, nextLine[2]);
                ps.executeUpdate();
            }

            // Populate questions
            reader = new CSVReader(new FileReader("questions.csv"));
            reader.readNext(); // Skip header
            while ((nextLine = reader.readNext()) != null) {
                PreparedStatement ps = c.prepareStatement("INSERT INTO questions (question_id, question_text, question_type) VALUES (?, ?, ?)");
                ps.setInt(1, Integer.parseInt(nextLine[0]));
                ps.setString(2, nextLine[1]);
                ps.setString(3, nextLine[2]);
                ps.executeUpdate();
            }

            // Populate answers
            reader = new CSVReader(new FileReader("answers.csv"));
            reader.readNext(); // Skip header
            while ((nextLine = reader.readNext()) != null) {
                PreparedStatement ps = c.prepareStatement("INSERT INTO answers (answer_id, question_id, answer_text, is_correct) VALUES (?, ?, ?, ?)");
                ps.setInt(1, Integer.parseInt(nextLine[0]));
                ps.setInt(2, Integer.parseInt(nextLine[1]));
                ps.setString(3, nextLine[2]);
                ps.setBoolean(4, Boolean.parseBoolean(nextLine[3]));
                ps.executeUpdate();
            }

            c.close();
        } catch (Exception e) {
            System.err.println(e.getClass().getName() + ": " + e.getMessage());
            System.exit(0);
        }
        System.out.println("Data populated successfully");
    }
}
