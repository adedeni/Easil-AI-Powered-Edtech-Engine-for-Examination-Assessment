import React, { useState } from "react";
import { 
  BarChart3,
  TrendingUp,
  Users,
  BookOpen,
  GraduationCap,
  Download,
  Filter,
  Calendar,
  ChevronDown
} from "lucide-react";

const Reports = () => {
  const [selectedPeriod, setSelectedPeriod] = useState("This Month");
  const [selectedReport, setSelectedReport] = useState("Overview");

  // Sample data for reports
  const overviewStats = {
    totalStudents: 300,
    totalLecturers: 15,
    totalCourses: 15,
    completedAssessments: 45,
    pendingAssessments: 8,
    averageScore: 78.5
  };

  const coursePerformance = [
    { course: "CSC 618 - E-Commerce", students: 120, avgScore: 85.2, passRate: 92 },
    { course: "CSC 601 - Advanced Programming", students: 95, avgScore: 78.5, passRate: 88 },
    { course: "CSC 602 - Data Structures", students: 110, avgScore: 82.1, passRate: 90 },
    { course: "CSC 603 - Algorithms", students: 88, avgScore: 75.8, passRate: 85 },
    { course: "CSC 604 - Software Engineering", students: 102, avgScore: 80.3, passRate: 89 }
  ];

  const lecturerPerformance = [
    { name: "Dr. Ikono Rhoda", courses: 2, students: 215, avgRating: 4.8, assessments: 12 },
    { name: "Prof. Michael Johnson", courses: 2, students: 183, avgRating: 4.6, assessments: 10 },
    { name: "Dr. Sarah Williams", courses: 2, students: 202, avgRating: 4.7, assessments: 11 },
    { name: "Dr. Ahmed Yusuf", courses: 2, students: 165, avgRating: 4.5, assessments: 9 }
  ];

  const recentActivity = [
    { date: "2024-11-20", activity: "Assessment completed", course: "CSC 618", participants: 118 },
    { date: "2024-11-19", activity: "New course added", course: "CSC 620", participants: 0 },
    { date: "2024-11-18", activity: "Lecturer assigned", course: "CSC 602", participants: 1 },
    { date: "2024-11-17", activity: "Assessment created", course: "CSC 601", participants: 95 },
    { date: "2024-11-16", activity: "Student enrolled", course: "Multiple", participants: 5 }
  ];

  return (
    <div className="bg-gray-50 min-h-screen p-8">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <div className="flex justify-between items-center mb-8">
          <div>
            <h1 className="text-2xl font-bold text-gray-800">Reports & Analytics</h1>
            <p className="text-gray-600 mt-1">Comprehensive system performance and usage analytics</p>
          </div>
          <div className="flex items-center space-x-4">
            <div className="flex items-center space-x-2">
              <Calendar className="w-4 h-4 text-gray-500" />
              <select 
                value={selectedPeriod}
                onChange={(e) => setSelectedPeriod(e.target.value)}
                className="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="This Week">This Week</option>
                <option value="This Month">This Month</option>
                <option value="This Quarter">This Quarter</option>
                <option value="This Year">This Year</option>
              </select>
            </div>
            <button className="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
              <Download className="w-4 h-4" />
              <span>Export Report</span>
            </button>
          </div>
        </div>

        {/* Report Navigation */}
        <div className="bg-white rounded-lg p-1 mb-8 shadow-sm">
          <div className="flex space-x-1">
            {["Overview", "Courses", "Lecturers", "Students", "Activity"].map((tab) => (
              <button
                key={tab}
                onClick={() => setSelectedReport(tab)}
                className={`px-6 py-3 rounded-lg font-medium transition-colors ${
                  selectedReport === tab
                    ? "bg-blue-600 text-white"
                    : "text-gray-600 hover:text-gray-900 hover:bg-gray-100"
                }`}
              >
                {tab}
              </button>
            ))}
          </div>
        </div>

        {/* Overview Report */}
        {selectedReport === "Overview" && (
          <div className="space-y-8">
            {/* Key Metrics */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
              <div className="bg-white rounded-lg p-6 shadow-sm">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-600">Total Students</p>
                    <p className="text-2xl font-bold text-blue-600">{overviewStats.totalStudents}</p>
                  </div>
                  <Users className="w-8 h-8 text-blue-600" />
                </div>
              </div>

              <div className="bg-white rounded-lg p-6 shadow-sm">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-600">Total Lecturers</p>
                    <p className="text-2xl font-bold text-green-600">{overviewStats.totalLecturers}</p>
                  </div>
                  <GraduationCap className="w-8 h-8 text-green-600" />
                </div>
              </div>

              <div className="bg-white rounded-lg p-6 shadow-sm">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-600">Total Courses</p>
                    <p className="text-2xl font-bold text-purple-600">{overviewStats.totalCourses}</p>
                  </div>
                  <BookOpen className="w-8 h-8 text-purple-600" />
                </div>
              </div>

              <div className="bg-white rounded-lg p-6 shadow-sm">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-600">Completed</p>
                    <p className="text-2xl font-bold text-orange-600">{overviewStats.completedAssessments}</p>
                  </div>
                  <BarChart3 className="w-8 h-8 text-orange-600" />
                </div>
              </div>

              <div className="bg-white rounded-lg p-6 shadow-sm">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-600">Pending</p>
                    <p className="text-2xl font-bold text-red-600">{overviewStats.pendingAssessments}</p>
                  </div>
                  <TrendingUp className="w-8 h-8 text-red-600" />
                </div>
              </div>

              <div className="bg-white rounded-lg p-6 shadow-sm">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-600">Avg Score</p>
                    <p className="text-2xl font-bold text-teal-600">{overviewStats.averageScore}%</p>
                  </div>
                  <div className="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center">
                    <span className="text-teal-600 font-bold">%</span>
                  </div>
                </div>
              </div>
            </div>

            {/* Performance Charts Placeholder */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
              <div className="bg-white rounded-lg p-6 shadow-sm">
                <h3 className="text-lg font-semibold text-gray-800 mb-4">Student Enrollment Trend</h3>
                <div className="h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                  <div className="text-center">
                    <BarChart3 className="w-12 h-12 text-gray-400 mx-auto mb-2" />
                    <p className="text-gray-500">Chart visualization would go here</p>
                  </div>
                </div>
              </div>

              <div className="bg-white rounded-lg p-6 shadow-sm">
                <h3 className="text-lg font-semibold text-gray-800 mb-4">Assessment Completion Rate</h3>
                <div className="h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                  <div className="text-center">
                    <TrendingUp className="w-12 h-12 text-gray-400 mx-auto mb-2" />
                    <p className="text-gray-500">Chart visualization would go here</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        )}

        {/* Courses Report */}
        {selectedReport === "Courses" && (
          <div className="bg-white rounded-lg shadow-sm">
            <div className="p-6 border-b border-gray-200">
              <h3 className="text-lg font-semibold text-gray-800">Course Performance Analysis</h3>
            </div>
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="bg-gray-50">
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Score</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pass Rate</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {coursePerformance.map((course, index) => (
                    <tr key={index} className="hover:bg-gray-50">
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm font-medium text-gray-900">{course.course}</div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{course.students}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{course.avgScore}%</td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="flex items-center">
                          <div className="text-sm text-gray-900">{course.passRate}%</div>
                          <div className="ml-2 w-16 bg-gray-200 rounded-full h-2">
                            <div 
                              className="bg-green-500 h-2 rounded-full" 
                              style={{ width: `${course.passRate}%` }}
                            ></div>
                          </div>
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                          course.passRate >= 90 ? 'bg-green-100 text-green-800' :
                          course.passRate >= 80 ? 'bg-yellow-100 text-yellow-800' :
                          'bg-red-100 text-red-800'
                        }`}>
                          {course.passRate >= 90 ? 'Excellent' : course.passRate >= 80 ? 'Good' : 'Needs Attention'}
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}

        {/* Lecturers Report */}
        {selectedReport === "Lecturers" && (
          <div className="bg-white rounded-lg shadow-sm">
            <div className="p-6 border-b border-gray-200">
              <h3 className="text-lg font-semibold text-gray-800">Lecturer Performance Analysis</h3>
            </div>
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="bg-gray-50">
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lecturer</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Courses</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assessments</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {lecturerPerformance.map((lecturer, index) => (
                    <tr key={index} className="hover:bg-gray-50">
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm font-medium text-gray-900">{lecturer.name}</div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{lecturer.courses}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{lecturer.students}</td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="flex items-center">
                          <span className="text-sm text-gray-900">{lecturer.avgRating}</span>
                          <div className="ml-1 flex">
                            {[...Array(5)].map((_, i) => (
                              <span key={i} className={`text-xs ${i < Math.floor(lecturer.avgRating) ? 'text-yellow-400' : 'text-gray-300'}`}>
                                ★
                              </span>
                            ))}
                          </div>
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{lecturer.assessments}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}

        {/* Activity Report */}
        {selectedReport === "Activity" && (
          <div className="bg-white rounded-lg shadow-sm">
            <div className="p-6 border-b border-gray-200">
              <h3 className="text-lg font-semibold text-gray-800">Recent System Activity</h3>
            </div>
            <div className="p-6">
              <div className="space-y-4">
                {recentActivity.map((activity, index) => (
                  <div key={index} className="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                    <div className="flex-shrink-0">
                      <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <BarChart3 className="w-5 h-5 text-blue-600" />
                      </div>
                    </div>
                    <div className="flex-1">
                      <div className="text-sm font-medium text-gray-900">{activity.activity}</div>
                      <div className="text-sm text-gray-500">Course: {activity.course}</div>
                    </div>
                    <div className="flex-shrink-0 text-right">
                      <div className="text-sm font-medium text-gray-900">{activity.participants} participants</div>
                      <div className="text-sm text-gray-500">{activity.date}</div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default Reports;
