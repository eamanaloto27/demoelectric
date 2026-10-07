# PowerFlow / demoElectric - Render deployment

This copy is prepared for Render's free Web Service + free PostgreSQL setup.

## Deployment
1. Create a GitHub repository and upload the contents of this folder (not the outer ZIP folder).
2. In Render, choose **New -> Blueprint** and select that GitHub repository.
3. Render will read `render.yaml` and create:
   - a free Docker web service using PHP 8.3
   - a free PostgreSQL database
4. Deploy the Blueprint.
5. The first visit automatically creates the database tables and imports the 25 demo customer accounts plus the `demoTeacher` login.

Demo login:
- Username: `demoTeacher`
- Password: `demoPassword`

The application reads `DATABASE_URL` from Render and uses PostgreSQL instead of the original local MySQL database.

## Important free-tier note
Render's free web service can sleep after inactivity, and Render's free PostgreSQL database currently expires after 30 days. The database is suitable for a school submission/demo, but it is not permanent free production hosting.

## Security
Do not commit a real `.env` file or database password to GitHub. Render injects `DATABASE_URL` automatically from the managed database.
