import React, { useState } from "react";
import { 
  Search, 
  Filter, 
  Plus,
  Eye,
  Edit,
  Trash2,
  X,
  Upload
} from "lucide-react";

const Courses = () => {
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedDepartment, setSelectedDepartment] = useState("Computer Science");
  const [selectedCourse, setSelectedCourse] = useState("CSC618");
  const [selectedDate, setSelectedDate] = useState("10/11/2024");
  const [showAddModal, setShowAddModal] = useState(false);
  const [showDetailsModal, setShowDetailsModal] = useState(false);
  const [selectedCourseDetails, setSelectedCourseDetails] = useState(null);

  // Sample courses data
  const [courses] = useState([
    {
      id: 1,
      code: "CSC 618",
      title: "Fundamentals of E-Commerce",
      department: "Computer Science",
      lecturer: "Ikono, Rhoda (PhD)",
      enrolledStudents: 120,
      status: "Active"
    },
    {
      id: 2,
      code: "CSC 618",
      title: "Fundamentals of E-Commerce",
      department: "Computer Science",
      lecturer: "Ikono, Rhoda (PhD)",
      enrolledStudents: 125,
      status: "Active"
    },
    {
      id: 3,
      code: "CSC 618",
      title: "Fundamentals of E-Commerce",
      department: "Computer Science",
      lecturer: "Ikono, Rhoda (PhD)",
      enrolledStudents: 120,
      status: "Active"
    },
    {
      id: 4,
      code: "CSC 618",
      title: "Fundamentals of E-Commerce",
      department: "Computer Science",
      lecturer: "Ikono, Rhoda (PhD)",
      enrolledStudents: 120,
      status: "Active"
    },
    {
      id: 5,
      code: "CSC 618",
      title: "Fundamentals of E-Commerce",
      department: "Computer Science",
      lecturer: "Ikono, Rhoda (PhD)",
      enrolledStudents: 120,
      status: "Active"
    }
  ]);

  const [newCourse, setNewCourse] = useState({
    title: "",
    code: "",
    description: "",
    status: "Active",
    lecturer: "",
    materials: null
  });

  const handleViewDetails = (course) => {
    setSelectedCourseDetails(course);
    setShowDetailsModal(true);
  };

  const handleAddCourse = () => {
    // Add course logic here
    console.log("Adding course:", newCourse);
    setNewCourse({
      title: "",
      code: "",
      description: "",
      status: "Active",
      lecturer: "",
      materials: null
    });
    setShowAddModal(false);
  };

  const AddCourseModal = () => {
    if (!showAddModal) return null;

    return (
      <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div className="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
          <div className="p-6">
            <h2 className="text-xl font-bold text-gray-800 mb-6">Add Course</h2>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Course Title
                </label>
                <input
                  type="text"
                  value={newCourse.title}
                  onChange={(e) => setNewCourse(prev => ({ ...prev, title: e.target.value }))}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Fundamentals of E-Commerce"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Course Code
                </label>
                <input
                  type="text"
                  value={newCourse.code}
                  onChange={(e) => setNewCourse(prev => ({ ...prev, code: e.target.value }))}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="CSC 618"
                />
              </div>
            </div>

            <div className="mb-6">
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Description
              </label>
              <textarea
                value={newCourse.description}
                onChange={(e) => setNewCourse(prev => ({ ...prev, description: e.target.value }))}
                rows={4}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="EC involves conducting transactions electronically. Its major categories are pure versus partial EC, Internet versus non-internet and electronic markets versus company-based systems..."
              />
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Status
                </label>
                <select
                  value={newCourse.status}
                  onChange={(e) => setNewCourse(prev => ({ ...prev, status: e.target.value }))}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Lecturers
                </label>
                <select
                  value={newCourse.lecturer}
                  onChange={(e) => setNewCourse(prev => ({ ...prev, lecturer: e.target.value }))}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Select Lecturer</option>
                  <option value="Ikono, Rhoda (PhD)">Ikono, Rhoda (PhD)</option>
                </select>
              </div>
            </div>

            <div className="mb-6">
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Upload Course Materials
              </label>
              <div className="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                <Upload className="w-8 h-8 text-gray-400 mx-auto mb-2" />
                <p className="text-gray-600 mb-2">Drag and drop files here or</p>
                <button className="text-blue-600 hover:text-blue-700">Choose files</button>
              </div>
            </div>

            <div className="flex justify-end space-x-4">
              <button 
                onClick={() => setShowAddModal(false)}
                className="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
              >
                Cancel
              </button>
              <button 
                onClick={handleAddCourse}
                className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
              >
                Save
              </button>
            </div>
          </div>
        </div>
      </div>
    );
  };

  const CourseDetailsModal = () => {
    if (!showDetailsModal || !selectedCourseDetails) return null;

    return (
      <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div className="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
          <div className="p-6">
            <div className="flex justify-between items-center mb-6">
              <h2 className="text-xl font-bold text-gray-800">Course Details</h2>
              <button 
                onClick={() => setShowDetailsModal(false)}
                className="text-gray-500 hover:text-gray-700"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            <div className="space-y-4 mb-6">
              <div className="flex items-center justify-between py-2 border-b border-gray-200">
                <span className="font-medium text-gray-700">Course Title:</span>
                <span className="text-gray-900">Fundamentals of E-Commerce</span>
              </div>
              <div className="flex items-center justify-between py-2 border-b border-gray-200">
                <span className="font-medium text-gray-700">Course Code:</span>
                <span className="text-gray-900">CSC 618</span>
              </div>
              <div className="flex items-center justify-between py-2 border-b border-gray-200">
                <span className="font-medium text-gray-700">Units:</span>
                <span className="text-gray-900">3</span>
              </div>
              <div className="flex items-center justify-between py-2 border-b border-gray-200">
                <span className="font-medium text-gray-700">Course Coordinator/Lecturers:</span>
                <span className="text-gray-900">Ikono, Rhoda (PhD)</span>
              </div>
              <div className="flex items-center justify-between py-2">
                <span className="font-medium text-gray-700">Status:</span>
                <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  Active
                </span>
              </div>
            </div>

            <div className="mb-6">
              <h3 className="font-semibold text-gray-800 mb-2">Course Description</h3>
              <div className="bg-gray-50 p-4 rounded-lg">
                <p className="text-gray-700">
                  EC involves conducting transactions electronically. Its major categories are pure versus partial EC, 
                  Internet versus non-internet and electronic markets versus company-based systems. The applications of EC, 
                  and there are many, are based on infrastructures and are supported by people, public policy and technical 
                  standards, marketing and advertising; support services, such as logistics, security, and payment services; 
                  and business partners—all tied together by management.
                </p>
              </div>
            </div>

            <div className="mb-6">
              <h3 className="font-semibold text-gray-800 mb-4">Course Material</h3>
              <div className="flex items-center space-x-2 text-blue-600">
                <span>📄</span>
                <span>Introduction to E-Commerce Module | Lecture 2 rev</span>
                <span className="text-gray-500">3.5MB</span>
              </div>
            </div>

            <div className="mb-6">
              <h3 className="font-semibold text-gray-800 mb-4">Overview</h3>
              <div className="grid grid-cols-3 gap-4">
                <div className="text-center p-4 bg-blue-50 rounded-lg">
                  <div className="text-2xl font-bold text-blue-600 mb-1">300</div>
                  <div className="text-sm text-gray-600">Total Students</div>
                </div>
                <div className="text-center p-4 bg-yellow-50 rounded-lg">
                  <div className="text-2xl font-bold text-yellow-600 mb-1">80</div>
                  <div className="text-sm text-gray-600">Average Score</div>
                </div>
                <div className="text-center p-4 bg-orange-50 rounded-lg">
                  <div className="text-2xl font-bold text-orange-600 mb-1">30</div>
                  <div className="text-sm text-gray-600">Total Absent Students</div>
                </div>
              </div>

              <div className="grid grid-cols-3 gap-4 mt-4">
                <div className="text-center p-4 bg-green-50 rounded-lg">
                  <div className="text-2xl font-bold text-green-600 mb-1">270</div>
                  <div className="text-sm text-gray-600">Total Finished Students</div>
                </div>
                <div className="text-center p-4 bg-green-50 rounded-lg">
                  <div className="text-2xl font-bold text-green-600 mb-1">250</div>
                  <div className="text-sm text-gray-600">Total Passed Students</div>
                </div>
                <div className="text-center p-4 bg-red-50 rounded-lg">
                  <div className="text-2xl font-bold text-red-600 mb-1">20</div>
                  <div className="text-sm text-gray-600">Total Failed Students</div>
                </div>
              </div>

              <div className="mt-4 text-right">
                <button className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                  Go to View Results →
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  };

  return (
    <div className="bg-gray-50 min-h-screen p-8">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <div className="flex justify-between items-center mb-8">
          <h1 className="text-2xl font-bold text-gray-800">Course Management</h1>
          <button 
            onClick={() => setShowAddModal(true)}
            className="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            <Plus className="w-4 h-4" />
            <span>Add New Course</span>
          </button>
        </div>

        {/* Filters */}
        <div className="bg-white rounded-lg p-6 mb-6 shadow-sm">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Keywords</label>
              <div className="relative">
                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
                <input
                  type="text"
                  placeholder="Search"
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Department</label>
              <select
                value={selectedDepartment}
                onChange={(e) => setSelectedDepartment(e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="Computer Science">Computer Science</option>
                <option value="Mathematics">Mathematics</option>
                <option value="Physics">Physics</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Course</label>
              <select
                value={selectedCourse}
                onChange={(e) => setSelectedCourse(e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="CSC618">CSC618</option>
                <option value="CSC601">CSC601</option>
                <option value="CSC602">CSC602</option>
              </select>
            </div>

            <div className="flex items-end space-x-2">
              <div className="flex-1">
                <label className="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <select
                  value={selectedDate}
                  onChange={(e) => setSelectedDate(e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="10/11/2024">10/11/2024</option>
                  <option value="11/11/2024">11/11/2024</option>
                  <option value="12/11/2024">12/11/2024</option>
                </select>
              </div>
              <button className="flex items-center space-x-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <Filter className="w-4 h-4" />
                <span>Filter</span>
              </button>
            </div>
          </div>
        </div>

        {/* Courses Table */}
        <div className="bg-white rounded-lg shadow-sm overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="bg-gray-600 text-white">
                  <th className="px-6 py-4 text-left">
                    <input type="checkbox" className="rounded" />
                  </th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Course Title/Code</th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Department</th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Lecturers</th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Enrolled Students</th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Status</th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Action</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {courses.map((course) => (
                  <tr key={course.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4">
                      <input type="checkbox" className="rounded" />
                    </td>
                    <td className="px-6 py-4">
                      <div>
                        <div className="text-sm font-medium text-gray-900">{course.code} -</div>
                        <div className="text-sm text-gray-900">{course.title}</div>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-900">{course.department}</td>
                    <td className="px-6 py-4 text-sm text-gray-900">{course.lecturer}</td>
                    <td className="px-6 py-4 text-sm text-gray-900">{course.enrolledStudents}</td>
                    <td className="px-6 py-4">
                      <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        ● {course.status}
                      </span>
                    </td>
                    <td className="px-6 py-4">
                      <div className="flex items-center space-x-2">
                        <button 
                          onClick={() => handleViewDetails(course)}
                          className="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                        >
                          View Details
                        </button>
                        <button className="p-1 text-gray-400 hover:text-gray-600">
                          <Edit className="w-4 h-4" />
                        </button>
                        <button className="p-1 text-gray-400 hover:text-red-600">
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {/* Modals */}
      <AddCourseModal />
      <CourseDetailsModal />
    </div>
  );
};

export default Courses;
