import { BrowserRouter as Router, Routes, Route, Navigate } from "react-router-dom";
import Login from "./dashboards/auth/Login";
import StudentRoutes from "./routes/studentRoutes";
import LecturerRoutes from "./routes/lecturerRoutes";
import AdminRoutes from "./routes/adminRoutes";

function App() {
  // Get user role from localStorage (set during login)
  const role = localStorage.getItem('userRole') || null;

  return (
    <Router>
      <Routes>
        <Route path="/login" element={<Login />} />
        <Route path="/student/*" element={<StudentRoutes />} />
        <Route path="/lecturer/*" element={<LecturerRoutes />} />
        <Route path="/admin/*" element={<AdminRoutes />} />
        <Route
          path="/"
          element={
            role === "student" ? <Navigate to="/student" />
              : role === "lecturer" ? <Navigate to="/lecturer" />
              : role === "admin" ? <Navigate to="/admin" />
              : <Navigate to="/login" />
          }
        />
      </Routes>
    </Router>
  );
}

export default App;
