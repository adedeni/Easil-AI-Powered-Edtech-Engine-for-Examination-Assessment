import React, { useState } from "react";
import { 
  Search, 
  Filter, 
  Plus,
  Edit,
  Trash2,
  X,
  Camera
} from "lucide-react";

const Teachers = () => {
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedDepartment, setSelectedDepartment] = useState("Computer Science");
  const [selectedCourse, setSelectedCourse] = useState("CSC616");
  const [showAddModal, setShowAddModal] = useState(false);
  const [showViewModal, setShowViewModal] = useState(false);
  const [selectedTeacher, setSelectedTeacher] = useState(null);

  // Sample teachers data
  const [teachers] = useState([
    {
      id: 1,
      name: "Ikono, Rhoda (PhD)",
      email: "adeola.adebayo@gmail.com",
      department: "Computer Science",
      courseAssigned: "CSC 616",
      avatar: "https://images.unsplash.com/photo-1494790108755-2616b612b786?w=40&h=40&fit=crop&crop=face"
    },
    {
      id: 2,
      name: "LecturerName 2",
      email: "adeola.adebayo@gmail.com",
      department: "Computer Science",
      courseAssigned: "CSC 601",
      avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face"
    },
    {
      id: 3,
      name: "LecturerName 3",
      email: "adeola.adebayo@gmail.com",
      department: "Computer Science",
      courseAssigned: "CSC 602",
      avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=40&h=40&fit=crop&crop=face"
    },
    {
      id: 4,
      name: "LecturerName 4",
      email: "adeola.adebayo@gmail.com",
      department: "Computer Science",
      courseAssigned: "CSC 603",
      avatar: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=40&h=40&fit=crop&crop=face"
    },
    {
      id: 5,
      name: "LecturerName 5",
      email: "adeola.adebayo@gmail.com",
      department: "Computer Science",
      courseAssigned: "CSC 604",
      avatar: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=40&h=40&fit=crop&crop=face"
    }
  ]);

  const [newTeacher, setNewTeacher] = useState({
    name: "",
    email: "",
    department: "Computer Science"
  });

  const [coursesAssigned, setCoursesAssigned] = useState([
    { id: 1, title: "Fundamentals of E-Commerce", code: "CSC 616", units: 3, selected: true },
    { id: 2, title: "Course 2", code: "CSC 602", units: 3, selected: false },
    { id: 3, title: "Course 3", code: "CSC 603", units: 3, selected: false },
    { id: 4, title: "Course 4", code: "CSC 604", units: 3, selected: false },
    { id: 5, title: "Course 5", code: "CSC 605", units: 3, selected: false }
  ]);

  const handleViewTeacher = (teacher) => {
    setSelectedTeacher(teacher);
    setShowViewModal(true);
  };

  const handleAddTeacher = () => {
    console.log("Adding teacher:", newTeacher);
    setNewTeacher({
      name: "",
      email: "",
      department: "Computer Science"
    });
    setShowAddModal(false);
  };

  const handleCourseSelection = (courseId) => {
    setCoursesAssigned(prev => 
      prev.map(course => 
        course.id === courseId 
          ? { ...course, selected: !course.selected }
          : course
      )
    );
  };

  const AddTeacherModal = () => {
    if (!showAddModal) return null;

    return (
      <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div className="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
          <div className="p-6">
            <h2 className="text-xl font-bold text-gray-800 mb-6">Add New Lecturer</h2>

            {/* Profile Picture Section */}
            <div className="flex items-center space-x-4 mb-6">
              <div className="w-20 h-20 bg-blue-400 rounded-full overflow-hidden">
                <img 
                  src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=80&h=80&fit=crop&crop=face" 
                  alt="Profile" 
                  className="w-full h-full object-cover"
                />
              </div>
              <button className="flex items-center space-x-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                <Camera className="w-4 h-4" />
                <span>Add Profile Picture</span>
              </button>
            </div>

            <div className="grid grid-cols-2 gap-4 mb-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Lecturer Name
                </label>
                <input
                  type="text"
                  value={newTeacher.name}
                  onChange={(e) => setNewTeacher(prev => ({ ...prev, name: e.target.value }))}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Lecturer Name 1"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Department
                </label>
                <select
                  value={newTeacher.department}
                  onChange={(e) => setNewTeacher(prev => ({ ...prev, department: e.target.value }))}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="Computer Science">Computer Science</option>
                  <option value="Mathematics">Mathematics</option>
                  <option value="Physics">Physics</option>
                </select>
              </div>
            </div>

            <div className="mb-6">
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Email
              </label>
              <input
                type="email"
                value={newTeacher.email}
                onChange={(e) => setNewTeacher(prev => ({ ...prev, email: e.target.value }))}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="lectureremail@email.com"
              />
            </div>

            {/* Courses Assigned Section */}
            <div className="mb-6">
              <h3 className="text-lg font-semibold text-gray-800 mb-4">Courses Assigned</h3>
              <div className="bg-gray-50 rounded-lg overflow-hidden">
                <div className="grid grid-cols-4 gap-4 p-3 bg-gray-600 text-white text-sm font-medium">
                  <div>Course Title</div>
                  <div>Course Code</div>
                  <div>Units</div>
                  <div>Selected</div>
                </div>
                {coursesAssigned.map((course) => (
                  <div key={course.id} className="grid grid-cols-4 gap-4 p-3 border-b border-gray-200">
                    <div className="text-sm text-gray-900">{course.title}</div>
                    <div className="text-sm text-gray-900">{course.code}</div>
                    <div className="text-sm text-gray-900">{course.units}</div>
                    <div>
                      <input
                        type="checkbox"
                        checked={course.selected}
                        onChange={() => handleCourseSelection(course.id)}
                        className="w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                      />
                    </div>
                  </div>
                ))}
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
                onClick={handleAddTeacher}
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

  const ViewTeacherModal = () => {
    if (!showViewModal || !selectedTeacher) return null;

    const teacherCourses = [
      { title: "Fundamentals of E-Commerce", code: "CSC 616", units: 3 }
    ];

    return (
      <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div className="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
          <div className="p-6">
            <div className="flex justify-between items-center mb-6">
              <h2 className="text-xl font-bold text-gray-800">Lecturer Details</h2>
              <button 
                onClick={() => setShowViewModal(false)}
                className="text-gray-500 hover:text-gray-700"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {/* Lecturer Info with Avatar */}
            <div className="flex items-start space-x-4 mb-6">
              <div className="w-20 h-20 bg-blue-400 rounded-full overflow-hidden flex-shrink-0">
                <img 
                  src={selectedTeacher.avatar} 
                  alt={selectedTeacher.name}
                  className="w-full h-full object-cover"
                />
              </div>
              <div className="flex-1 space-y-3">
                <div className="flex items-center">
                  <span className="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                  <span className="text-sm font-medium text-gray-700">Name:</span>
                  <span className="text-sm text-gray-900 ml-2">Ikono, Rhoda (PhD)</span>
                  <div className="ml-auto">
                    <span className="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full">
                      ● Active
                    </span>
                  </div>
                </div>
                <div className="flex items-center">
                  <span className="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                  <span className="text-sm font-medium text-gray-700">Department:</span>
                  <span className="text-sm text-gray-900 ml-2">Computer Science</span>
                </div>
                <div className="flex items-center">
                  <span className="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                  <span className="text-sm font-medium text-gray-700">Email:</span>
                  <span className="text-sm text-gray-900 ml-2">lectureremail@email.com</span>
                </div>
              </div>
            </div>

            {/* Courses Assigned Section */}
            <div className="mb-6">
              <h3 className="text-lg font-semibold text-gray-800 mb-4">Courses Assigned</h3>
              <div className="bg-gray-50 rounded-lg overflow-hidden">
                <div className="grid grid-cols-3 gap-4 p-3 bg-gray-600 text-white text-sm font-medium">
                  <div>Course Title</div>
                  <div>Course Code</div>
                  <div>Units</div>
                </div>
                {teacherCourses.map((course, index) => (
                  <div key={index} className="grid grid-cols-3 gap-4 p-3 border-b border-gray-200">
                    <div className="text-sm text-gray-900">{course.title}</div>
                    <div className="text-sm text-gray-900">{course.code}</div>
                    <div className="text-sm text-gray-900">{course.units}</div>
                  </div>
                ))}
              </div>
            </div>

            <div className="flex justify-end">
              <button 
                onClick={() => setShowViewModal(false)}
                className="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
              >
                Cancel
              </button>
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
          <h1 className="text-2xl font-bold text-gray-800">Manage Lecturers</h1>
          <button 
            onClick={() => setShowAddModal(true)}
            className="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            <Plus className="w-4 h-4" />
            <span>Add New Lecturer</span>
          </button>
        </div>

        {/* Filters */}
        <div className="bg-white rounded-lg p-6 mb-6 shadow-sm">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
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

            <div className="flex items-end space-x-2">
              <div className="flex-1">
                <label className="block text-sm font-medium text-gray-700 mb-2">Course</label>
                <input
                  type="text"
                  value={selectedCourse}
                  onChange={(e) => setSelectedCourse(e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="CSC616"
                />
              </div>
              <button className="flex items-center space-x-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <Filter className="w-4 h-4" />
                <span>Filter</span>
              </button>
            </div>
          </div>
        </div>

        {/* Teachers Table */}
        <div className="bg-white rounded-lg shadow-sm overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="bg-gray-600 text-white">
                  <th className="px-6 py-4 text-left text-sm font-medium">Name</th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Email</th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Department</th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Course Assigned</th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Details</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {teachers.map((teacher) => (
                  <tr key={teacher.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4">
                      <div className="flex items-center space-x-3">
                        <div className="w-10 h-10 rounded-full overflow-hidden">
                          <img 
                            src={teacher.avatar} 
                            alt={teacher.name}
                            className="w-full h-full object-cover"
                          />
                        </div>
                        <span className="text-sm font-medium text-gray-900">{teacher.name}</span>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-900">{teacher.email}</td>
                    <td className="px-6 py-4 text-sm text-gray-900">{teacher.department}</td>
                    <td className="px-6 py-4 text-sm text-gray-900">{teacher.courseAssigned}</td>
                    <td className="px-6 py-4">
                      <div className="flex items-center space-x-2">
                        <button 
                          onClick={() => handleViewTeacher(teacher)}
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
      <AddTeacherModal />
      <ViewTeacherModal />
    </div>
  );
};

export default Teachers;
