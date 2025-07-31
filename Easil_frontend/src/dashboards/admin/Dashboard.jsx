import React from "react";
import { 
  Users, 
  GraduationCap, 
  BookOpen,
  ChevronRight
} from "lucide-react";

const Dashboard = () => {
  return (
    <div className="min-h-screen">
      <div className="max-w-6xl mx-auto">
        {/* Stats Cards Row */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
          {/* Total Students Card */}
          <div className="bg-white rounded-lg shadow-sm p-4 sm:p-6 border border-gray-200">
            <div className="flex items-center space-x-3 sm:space-x-4">
              <div className="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                <Users className="w-5 h-5 sm:w-6 sm:h-6 text-gray-600" />
              </div>
              <div className="min-w-0">
                <p className="text-xs sm:text-sm text-gray-500 font-medium">Total Students</p>
                <p className="text-xl sm:text-2xl font-bold text-gray-900">300</p>
              </div>
            </div>
          </div>

          {/* Total Lecturers Card */}
          <div className="bg-white rounded-lg shadow-sm p-4 sm:p-6 border border-gray-200">
            <div className="flex items-center space-x-3 sm:space-x-4">
              <div className="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                <GraduationCap className="w-5 h-5 sm:w-6 sm:h-6 text-gray-600" />
              </div>
              <div className="min-w-0">
                <p className="text-xs sm:text-sm text-gray-500 font-medium">Total Lecturers</p>
                <p className="text-xl sm:text-2xl font-bold text-gray-900">15</p>
              </div>
            </div>
          </div>

          {/* Total Courses Card */}
          <div className="bg-white rounded-lg shadow-sm p-4 sm:p-6 border border-gray-200">
            <div className="flex items-center space-x-3 sm:space-x-4">
              <div className="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                <BookOpen className="w-5 h-5 sm:w-6 sm:h-6 text-gray-600" />
              </div>
              <div className="min-w-0">
                <p className="text-xs sm:text-sm text-gray-500 font-medium">Total Courses</p>
                <p className="text-xl sm:text-2xl font-bold text-gray-900">15</p>
              </div>
            </div>
          </div>
        </div>

        {/* Management Cards Row */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
          {/* Manage Lecturers Card */}
          <div className="bg-teal-600 rounded-lg p-4 sm:p-6 text-white cursor-pointer hover:bg-teal-700 transition-colors group">
            <div className="flex items-start justify-between mb-3 sm:mb-4">
              <div className="w-8 h-8 sm:w-10 sm:h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center flex-shrink-0">
                <GraduationCap className="w-4 h-4 sm:w-5 sm:h-5 text-white" />
              </div>
              <ChevronRight className="w-4 h-4 sm:w-5 sm:h-5 text-white opacity-70 group-hover:translate-x-1 transition-transform" />
            </div>
            <h3 className="text-lg sm:text-xl font-bold mb-2">Manage Lecturers</h3>
            <p className="text-white text-opacity-90 text-xs sm:text-sm leading-relaxed">
              Oversee and update lecturer profiles and course assignments
            </p>
          </div>

          {/* Manage Students Card */}
          <div className="bg-green-600 rounded-lg p-4 sm:p-6 text-white cursor-pointer hover:bg-green-700 transition-colors group">
            <div className="flex items-start justify-between mb-3 sm:mb-4">
              <div className="w-8 h-8 sm:w-10 sm:h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center flex-shrink-0">
                <Users className="w-4 h-4 sm:w-5 sm:h-5 text-white" />
              </div>
              <ChevronRight className="w-4 h-4 sm:w-5 sm:h-5 text-white opacity-70 group-hover:translate-x-1 transition-transform" />
            </div>
            <h3 className="text-lg sm:text-xl font-bold mb-2">Manage Students</h3>
            <p className="text-white text-opacity-90 text-xs sm:text-sm leading-relaxed">
              Access and analyze student assessment results
            </p>
          </div>

          {/* Manage Courses Card */}
          <div className="bg-orange-600 rounded-lg p-4 sm:p-6 text-white cursor-pointer hover:bg-orange-700 transition-colors group">
            <div className="flex items-start justify-between mb-3 sm:mb-4">
              <div className="w-8 h-8 sm:w-10 sm:h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center flex-shrink-0">
                <BookOpen className="w-4 h-4 sm:w-5 sm:h-5 text-white" />
              </div>
              <ChevronRight className="w-4 h-4 sm:w-5 sm:h-5 text-white opacity-70 group-hover:translate-x-1 transition-transform" />
            </div>
            <h3 className="text-lg sm:text-xl font-bold mb-2">Manage Courses</h3>
            <p className="text-white text-opacity-90 text-xs sm:text-sm leading-relaxed">
              Access and analyze courses and results
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;

