import React, { useState } from "react";
import { 
  Users, 
  BookOpen, 
  FileText, 
  TrendingUp,
  Search,
  Filter,
  Eye,
  CheckCircle,
  XCircle,
  Clock
} from "lucide-react";

const Reports = () => {
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedFilter, setSelectedFilter] = useState("All");
  const [showStudentDetails, setShowStudentDetails] = useState(false);
  const [selectedStudent, setSelectedStudent] = useState(null);

  // Sample exam data
  const examData = {
    title: "Mid-Semester Exam",
    courseTitle: "Fundamentals of E-Commerce",
    courseCode: "CSC 616",
    units: "3",
    date: "06 September, 2024",
    time: "8:00AM - 12:00AM",
    duration: "30 mins",
    totalQuestions: "25 questions",
    stats: {
      totalStudents: 300,
      averageScore: 80,
      totalAbsentStudents: 30,
      totalFinishedStudents: 270,
      totalPassedStudents: 250,
      totalFailedStudents: 20
    }
  };

  // Sample student results
  const studentResults = [
    { 
      id: 1, 
      name: "Blessing Sharon", 
      status: "Passed", 
      score: "20/25 (80%)", 
      grade: "Excellent",
      avatar: "👩‍🎓",
      regNumber: "CSC/2024/001",
      department: "CSC 616",
      email: "blessing.sharon@email.com",
      detailedResults: {
        totalQuestions: 10,
        correctAnswers: 8,
        incorrectAnswers: 2,
        timeTaken: "10 mins 43 secs"
      }
    },
    { 
      id: 2, 
      name: "Temitope Adefuyi", 
      status: "Failed", 
      score: "12/25 (48%)", 
      grade: "Poor",
      avatar: "👨‍🎓",
      regNumber: "CSC/2024/002",
      department: "CSC 616", 
      email: "temitope.adefuyi@email.com",
      detailedResults: {
        totalQuestions: 10,
        correctAnswers: 4,
        incorrectAnswers: 6,
        timeTaken: "15 mins 20 secs"
      }
    },
    { 
      id: 3, 
      name: "James Benson", 
      status: "Passed", 
      score: "18/25 (72%)", 
      grade: "Average",
      avatar: "👨‍🎓",
      regNumber: "CSC/2024/003",
      department: "CSC 616",
      email: "james.benson@email.com",
      detailedResults: {
        totalQuestions: 10,
        correctAnswers: 7,
        incorrectAnswers: 3,
        timeTaken: "12 mins 15 secs"
      }
    }
  ];

  const filteredResults = studentResults.filter(student => {
    const matchesSearch = student.name.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesFilter = selectedFilter === "All" || student.status === selectedFilter;
    return matchesSearch && matchesFilter;
  });

  const getStatusColor = (status) => {
    switch (status) {
      case "Passed": return "text-green-600";
      case "Failed": return "text-red-600";
      default: return "text-gray-600";
    }
  };

  const getGradeColor = (grade) => {
    switch (grade) {
      case "Excellent": return "text-green-600";
      case "Average": return "text-blue-600";
      case "Poor": return "text-red-600";
      default: return "text-gray-600";
    }
  };

  const handleViewDetails = (student) => {
    setSelectedStudent(student);
    setShowStudentDetails(true);
  };

  const StudentDetailsModal = () => {
    if (!showStudentDetails || !selectedStudent) return null;

    return (
      <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div className="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
          {/* Header */}
          <div className="flex justify-between items-center p-6 border-b border-gray-200">
            <h2 className="text-xl font-bold text-gray-800">Student Details</h2>
            <button 
              onClick={() => setShowStudentDetails(false)}
              className="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors"
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div className="p-6">
            {/* Student Profile Section */}
            <div className="flex items-start space-x-6 mb-8">
              <div className="relative">
                <div className="w-20 h-20 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center shadow-lg">
                  <span className="text-white text-2xl font-bold">
                    {selectedStudent.name.split(' ').map(n => n[0]).join('')}
                  </span>
                </div>
                <div className="absolute -top-1 -right-1">
                  <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Active
                  </span>
                </div>
              </div>
              
              <div className="flex-1">
                <h3 className="text-lg font-semibold text-gray-900 mb-4">Student Information</h3>
                <div className="grid grid-cols-1 gap-3">
                  <div className="flex items-center space-x-3">
                    <div className="w-4 h-4 border-2 border-gray-400 rounded-sm"></div>
                    <span className="text-sm font-medium text-gray-600 w-32">Name:</span>
                    <span className="text-gray-900 font-medium">{selectedStudent.name}</span>
                  </div>
                  <div className="flex items-center space-x-3">
                    <div className="w-4 h-4 border-2 border-gray-400 rounded-sm"></div>
                    <span className="text-sm font-medium text-gray-600 w-32">Registration Number:</span>
                    <span className="text-gray-900 font-medium">{selectedStudent.regNumber}</span>
                  </div>
                  <div className="flex items-center space-x-3">
                    <div className="w-4 h-4 border-2 border-gray-400 rounded-sm"></div>
                    <span className="text-sm font-medium text-gray-600 w-32">Department:</span>
                    <span className="text-gray-900 font-medium">{selectedStudent.department}</span>
                  </div>
                  <div className="flex items-center space-x-3">
                    <div className="w-4 h-4 border-2 border-gray-400 rounded-sm"></div>
                    <span className="text-sm font-medium text-gray-600 w-32">Student Email:</span>
                    <span className="text-gray-900 font-medium">{selectedStudent.email}</span>
                  </div>
                </div>
              </div>
            </div>

            {/* Assessment Review Section */}
            <div className="mb-8">
              <div className="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold mb-6 shadow-md">
                CSC 616 Assessment Review
              </div>

              <div className="bg-gray-50 rounded-lg p-6">
                <div className="grid grid-cols-2 gap-6">
                  <div className="flex justify-between items-center py-2 border-b border-gray-200">
                    <span className="text-gray-600 font-medium">Total Number of Questions</span>
                    <span className="text-gray-900 font-bold text-lg">{selectedStudent.detailedResults.totalQuestions}</span>
                  </div>
                  <div className="flex justify-between items-center py-2 border-b border-gray-200">
                    <span className="text-gray-600 font-medium">Total Number of Correct Answers</span>
                    <span className="text-green-600 font-bold text-lg">{selectedStudent.detailedResults.correctAnswers}</span>
                  </div>
                  <div className="flex justify-between items-center py-2 border-b border-gray-200">
                    <span className="text-gray-600 font-medium">Total Number of Incorrect Answers</span>
                    <span className="text-red-600 font-bold text-lg">{selectedStudent.detailedResults.incorrectAnswers}</span>
                  </div>
                  <div className="flex justify-between items-center py-2 border-b border-gray-200">
                    <span className="text-gray-600 font-medium">Total Time Taken</span>
                    <span className="text-blue-600 font-bold text-lg">{selectedStudent.detailedResults.timeTaken}</span>
                  </div>
                </div>
              </div>
            </div>

            {/* Answers & Feedbacks Section */}
            <div className="mb-8">
              <div className="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold mb-6 shadow-md">
                Answers & Feedbacks
              </div>

              <div className="space-y-6">
                <div className="border border-gray-200 rounded-lg p-6 bg-white shadow-sm">
                  <h3 className="font-bold text-gray-800 mb-3 text-lg">Question 1 of 10</h3>
                  <p className="text-gray-600 mb-4 italic">Define EC and e-business.</p>
                  
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <h4 className="font-semibold text-gray-700 mb-3 text-sm uppercase tracking-wide">Student's Answer</h4>
                      <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-gray-700 leading-relaxed">
                        E-commerce is defined as the buying and selling of goods/services online.
                        While E-business is conducting all business operations online, including buying, selling and managing.
                      </div>
                    </div>
                    <div>
                      <h4 className="font-semibold text-gray-700 mb-3 text-sm uppercase tracking-wide">Feedback</h4>
                      <div className="bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-gray-700 leading-relaxed">
                        Clear and concise definitions!
                      </div>
                    </div>
                  </div>
                </div>

                <div className="border border-gray-200 rounded-lg p-6 bg-white shadow-sm">
                  <h3 className="font-bold text-gray-800 mb-3 text-lg">Question 2 of 10</h3>
                  <p className="text-gray-600 mb-4 italic">Define EC and e-business.</p>
                  
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <h4 className="font-semibold text-gray-700 mb-3 text-sm uppercase tracking-wide">Student's Answer</h4>
                      <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-gray-700 leading-relaxed">
                        E-commerce is defined as the buying and selling of goods/services online.
                        While E-business is conducting all business operations online, including buying, selling and managing.
                      </div>
                    </div>
                    <div>
                      <h4 className="font-semibold text-gray-700 mb-3 text-sm uppercase tracking-wide">Feedback</h4>
                      <div className="bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-gray-700 leading-relaxed">
                        Clear and concise definitions!
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Action Buttons */}
          <div className="flex justify-end space-x-4 p-6 border-t border-gray-200 bg-gray-50">
            <button 
              onClick={() => setShowStudentDetails(false)}
              className="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200 font-medium"
            >
              Cancel
            </button>
            <button className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:shadow-lg transition-all duration-200 font-medium">
              Save
            </button>
          </div>
        </div>
      </div>
    );
  };

  return (
    <div className="bg-gray-50 min-h-screen">
      <div className="max-w-7xl mx-auto px-6 py-8">
        <h1 className="text-2xl font-semibold text-gray-800 mb-8">Grading & Reports</h1>

        {/* Exam Information Card */}
        <div className="bg-white rounded-lg border border-gray-200 p-6 mb-8">
          <h2 className="text-xl font-semibold text-gray-800 mb-4">Exam Title: {examData.title}</h2>
          
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div className="flex items-center space-x-2">
              <BookOpen className="h-5 w-5 text-gray-500" />
              <span className="text-sm text-gray-600">Course Title</span>
              <span className="font-medium">{examData.courseTitle}</span>
            </div>
            <div className="flex items-center space-x-2">
              <FileText className="h-5 w-5 text-gray-500" />
              <span className="text-sm text-gray-600">Course Code</span>
              <span className="font-medium">{examData.courseCode}</span>
            </div>
            <div className="flex items-center space-x-2">
              <span className="text-sm text-gray-600">Units</span>
              <span className="font-medium">{examData.units}</span>
            </div>
          </div>

          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-6">
              <div className="flex items-center space-x-2">
                <span className="text-sm text-gray-600">Date:</span>
                <span className="font-medium">{examData.date}</span>
              </div>
              <div className="flex items-center space-x-2">
                <span className="text-sm text-gray-600">Time:</span>
                <span className="font-medium">{examData.time}</span>
              </div>
            </div>
            <div className="flex items-center space-x-4">
              <span className="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">{examData.duration}</span>
              <span className="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">{examData.totalQuestions}</span>
            </div>
          </div>
        </div>

        {/* Statistics Cards */}
        <div className="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
          <div className="bg-white rounded-lg border border-gray-200 p-4 text-center">
            <div className="flex items-center justify-center mb-2">
              <Users className="h-8 w-8 text-blue-600" />
            </div>
            <div className="text-sm text-gray-600">Total Students</div>
            <div className="text-2xl font-semibold text-gray-800">{examData.stats.totalStudents}</div>
          </div>

          <div className="bg-white rounded-lg border border-gray-200 p-4 text-center">
            <div className="flex items-center justify-center mb-2">
              <TrendingUp className="h-8 w-8 text-yellow-600" />
            </div>
            <div className="text-sm text-gray-600">Average Score</div>
            <div className="text-2xl font-semibold text-gray-800">{examData.stats.averageScore}</div>
          </div>

          <div className="bg-white rounded-lg border border-gray-200 p-4 text-center">
            <div className="flex items-center justify-center mb-2">
              <XCircle className="h-8 w-8 text-orange-600" />
            </div>
            <div className="text-sm text-gray-600">Total Absent Students</div>
            <div className="text-2xl font-semibold text-gray-800">{examData.stats.totalAbsentStudents}</div>
          </div>

          <div className="bg-white rounded-lg border border-gray-200 p-4 text-center">
            <div className="flex items-center justify-center mb-2">
              <CheckCircle className="h-8 w-8 text-blue-600" />
            </div>
            <div className="text-sm text-gray-600">Total Finished Students</div>
            <div className="text-2xl font-semibold text-gray-800">{examData.stats.totalFinishedStudents}</div>
          </div>

          <div className="bg-white rounded-lg border border-gray-200 p-4 text-center">
            <div className="flex items-center justify-center mb-2">
              <CheckCircle className="h-8 w-8 text-green-600" />
            </div>
            <div className="text-sm text-gray-600">Total Passed Students</div>
            <div className="text-2xl font-semibold text-gray-800">{examData.stats.totalPassedStudents}</div>
          </div>

          <div className="bg-white rounded-lg border border-gray-200 p-4 text-center">
            <div className="flex items-center justify-center mb-2">
              <XCircle className="h-8 w-8 text-red-600" />
            </div>
            <div className="text-sm text-gray-600">Total Failed Students</div>
            <div className="text-2xl font-semibold text-gray-800">{examData.stats.totalFailedStudents}</div>
          </div>
        </div>

        {/* Exam Results Section */}
        <div className="bg-white rounded-lg border border-gray-200 p-6">
          <div className="flex justify-between items-center mb-6">
            <div>
              <h2 className="text-xl font-semibold text-gray-800">Exam Results</h2>
              <p className="text-gray-600">Exam results aren't published yet</p>
            </div>
            <div className="flex space-x-3">
              <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Publish With Feedbacks
              </button>
              <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Publish Without Feedbacks
              </button>
            </div>
          </div>

          {/* Search and Filter */}
          <div className="flex justify-between items-center mb-4">
            <div className="flex items-center space-x-4">
              <div className="relative">
                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
                <input
                  type="text"
                  placeholder="Search"
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <select
                value={selectedFilter}
                onChange={(e) => setSelectedFilter(e.target.value)}
                className="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="All">All</option>
                <option value="Passed">Passed</option>
                <option value="Failed">Failed</option>
              </select>
              <button className="flex items-center space-x-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                <Filter className="h-4 w-4" />
                <span>Filter</span>
              </button>
            </div>
            <div className="text-sm text-gray-600">
              Showing {filteredResults.length} results
            </div>
          </div>

          {/* Results Table */}
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="bg-gray-600 text-white">
                  <th className="px-4 py-3 text-left text-sm font-medium">Student Name</th>
                  <th className="px-4 py-3 text-left text-sm font-medium">Passed/Failed</th>
                  <th className="px-4 py-3 text-left text-sm font-medium">Score</th>
                  <th className="px-4 py-3 text-left text-sm font-medium">Grade</th>
                  <th className="px-4 py-3 text-left text-sm font-medium">Details</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {filteredResults.map((student) => (
                  <tr key={student.id} className="hover:bg-gray-50">
                    <td className="px-4 py-4">
                      <div className="flex items-center space-x-3">
                        <div className="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center text-white text-sm">
                          {student.name.charAt(0)}
                        </div>
                        <span className="text-sm text-gray-900">{student.name}</span>
                      </div>
                    </td>
                    <td className="px-4 py-4">
                      <span className={`text-sm font-medium ${getStatusColor(student.status)}`}>
                        {student.status}
                      </span>
                    </td>
                    <td className="px-4 py-4">
                      <span className="text-sm text-gray-900">{student.score}</span>
                    </td>
                    <td className="px-4 py-4">
                      <span className={`text-sm font-medium ${getGradeColor(student.grade)}`}>
                        {student.grade}
                      </span>
                    </td>
                    <td className="px-4 py-4">
                      <button 
                        onClick={() => handleViewDetails(student)}
                        className="text-blue-600 hover:text-blue-700 text-sm font-medium"
                      >
                        View Details
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {/* Student Details Modal */}
      <StudentDetailsModal />
    </div>
  );
};

export default Reports;
