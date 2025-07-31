import { Route, Routes } from "react-router-dom";
import StudentLayout from "../layouts/StudentLayout";
import Home from "../dashboards/student/Home";
import Assessment from "../dashboards/student/Assessments";
import AssessmentDetails from "../dashboards/student/AssessmentDetails";
import TestInterface from "../dashboards/student/TestInterface";
import AssessmentComplete from "../dashboards/student/AssessmentComplete";
import Results from "../dashboards/student/Results";

export default function StudentRoutes() {
  return (
    <Routes>
      <Route path="/" element={<StudentLayout />}>
        <Route index element={<Home />} />
        <Route path="assessments" element={<Assessment />} />
        <Route path="assessments/:id" element={<AssessmentDetails />} />
        <Route path="assessments/:id/test" element={<TestInterface />} />
        <Route path="assessments/:id/complete" element={<AssessmentComplete />} />
        <Route path="results" element={<Results />} />
      </Route>
    </Routes>
  );
}
