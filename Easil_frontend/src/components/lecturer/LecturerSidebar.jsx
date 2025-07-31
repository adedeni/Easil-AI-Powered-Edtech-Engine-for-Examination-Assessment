import { NavLink, useNavigate } from "react-router-dom";
import { Home, Users, BookOpen, FileText, BarChart3, LogOut, X } from "lucide-react";

const LecturerSidebar = ({ isMobileMenuOpen, onItemClick }) => {
  const navigate = useNavigate();

  const handleLogout = () => {
    localStorage.removeItem('userRole');
    localStorage.removeItem('username');
    navigate('/login');
    if (onItemClick) onItemClick();
  };

  const links = [
    { name: "Home", path: "/lecturer", icon: <Home size={20} /> },
    { name: "Courses", path: "/lecturer/courses", icon: <BookOpen size={20} /> },
    { name: "Exams", path: "/lecturer/exam-management", icon: <FileText size={20} /> },
    { name: "Grading & Reports", path: "/lecturer/reports", icon: <BarChart3 size={20} /> },
    { name: "Students", path: "/lecturer/students", icon: <Users size={20} /> }
  ];

  return (
    <div className={`fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 ${
      isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'
    }`}>
      {/* Close button for mobile */}
      <div className="lg:hidden p-4 border-b border-gray-200 flex justify-end">
        <button
          onClick={onItemClick}
          className="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100"
        >
          <X className="w-5 h-5" />
        </button>
      </div>

      <nav className="p-3 sm:p-4 space-y-2">
        {links.map((link) => (
          <NavLink
            key={link.name}
            to={link.path}
            onClick={onItemClick}
            className={({ isActive }) =>
              `flex items-center space-x-3 px-3 py-2 sm:py-3 rounded-lg transition-colors text-sm sm:text-base ${
                isActive
                  ? "bg-blue-50 text-blue-600"
                  : "text-gray-600 hover:bg-gray-100 hover:text-gray-800"
              }`
            }
          >
            <span className="flex-shrink-0">{link.icon}</span>
            <span>{link.name}</span>
          </NavLink>
        ))}
        
        {/* Logout */}
        <div className="pt-6 sm:pt-8">
          <button 
            onClick={handleLogout}
            className="flex items-center space-x-3 px-3 py-2 sm:py-3 text-gray-600 hover:bg-gray-100 hover:text-gray-800 rounded-lg transition-colors w-full text-left text-sm sm:text-base"
          >
            <LogOut size={20} className="flex-shrink-0" />
            <span>Logout</span>
          </button>
        </div>
      </nav>
    </div>
  );
};

export default LecturerSidebar;
