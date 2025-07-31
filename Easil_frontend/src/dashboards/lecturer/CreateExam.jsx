import React, { useState } from "react";
import { 
  ArrowLeft, 
  Upload, 
  Edit, 
  Trash2, 
  Loader2,
  Sparkles
} from "lucide-react";
import { useNavigate } from "react-router-dom";

const CreateExam = () => {
  const navigate = useNavigate();
  const [currentStep, setCurrentStep] = useState(1);
  const [isGenerating, setIsGenerating] = useState(false);
  const [showManualCreation, setShowManualCreation] = useState(false);
  
  // Basic exam information
  const [examData, setExamData] = useState({
    title: "",
    course: "",
    numberOfQuestions: "",
    duration: "",
    deadline: ""
  });

  // LLM-powered question generation
  const [llmData, setLlmData] = useState({
    questionType: "Short Answer",
    numberOfQuestions: 10,
    uploadedMaterial: null
  });

  // Manual question creation
  const [manualQuestions, setManualQuestions] = useState([
    {
      id: 1,
      text: "",
      type: "Short Answer"
    }
  ]);

  // Generated questions from LLM
  const [generatedQuestions, setGeneratedQuestions] = useState([
    {
      id: 1,
      text: "Define EC and e-business.",
      type: "Long Answer",
      selected: true
    },
    {
      id: 2,
      text: "Define EC and e-business.",
      type: "Short Answer",
      selected: true
    },
    {
      id: 3,
      text: "Define EC and e-business.",
      type: "Short Answer",
      selected: true
    },
    {
      id: 4,
      text: "Define EC and e-business.",
      type: "Long Answer",
      selected: true
    },
    {
      id: 5,
      text: "Define EC and e-business.",
      type: "Long Answer",
      selected: true
    }
  ]);

  const courses = [
    { value: "csc616", label: "Fundamentals of E-Commerce - CSC 616" },
    { value: "csc601", label: "Computer Science Fundamentals - CSC 601" }
  ];

  const questionTypes = ["Short Answer", "Long Answer", "Multiple Choice", "True/False"];

  const handleExamDataChange = (field, value) => {
    setExamData(prev => ({ ...prev, [field]: value }));
  };

  const handleLlmDataChange = (field, value) => {
    setLlmData(prev => ({ ...prev, [field]: value }));
  };

  const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
      setLlmData(prev => ({ ...prev, uploadedMaterial: file }));
    }
  };

  const generateQuestionsWithLLM = async () => {
    setIsGenerating(true);
    setTimeout(() => {
      setIsGenerating(false);
      setCurrentStep(4);
    }, 3000);
  };

  const updateManualQuestion = (id, field, value) => {
    setManualQuestions(prev => 
      prev.map(q => q.id === id ? { ...q, [field]: value } : q)
    );
  };

  const toggleQuestionSelection = (id) => {
    setGeneratedQuestions(prev =>
      prev.map(q => q.id === id ? { ...q, selected: !q.selected } : q)
    );
  };

  const saveDraft = () => {
    console.log("Saving draft...");
    alert("Draft saved successfully!");
  };

  const publishExam = () => {
    console.log("Publishing exam...");
    alert("Exam published successfully!");
    navigate('/lecturer/exam-management');
  };

  // Step 1: Basic Exam Information
  const renderBasicInformation = () => (
    <div className="bg-white rounded-lg border border-gray-200 p-8">
      <h2 className="text-xl font-semibold text-gray-800 mb-6">Basic Exam Information</h2>
      
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">Exam Title</label>
          <input
            type="text"
            value={examData.title}
            onChange={(e) => handleExamDataChange('title', e.target.value)}
            placeholder="Mid Semester Assessment"
            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
          <p className="text-xs text-gray-500 mt-1">Enter exam title</p>
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">Select Course</label>
          <select
            value={examData.course}
            onChange={(e) => handleExamDataChange('course', e.target.value)}
            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="">Fundamentals of E-Commerce - CSC 616</option>
            {courses.map(course => (
              <option key={course.value} value={course.value}>
                {course.label}
              </option>
            ))}
          </select>
          <p className="text-xs text-gray-500 mt-1">Choose the relevant course</p>
        </div>
      </div>

      <div className="mt-8">
        <h3 className="text-lg font-medium text-gray-800 mb-4">Question Settings</h3>
        
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">Number of Questions</label>
            <input
              type="number"
              value={examData.numberOfQuestions}
              onChange={(e) => handleExamDataChange('numberOfQuestions', e.target.value)}
              placeholder="10"
              className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
            <p className="text-xs text-gray-500 mt-1">Total number of questions</p>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">Exam Duration</label>
            <input
              type="text"
              value={examData.duration}
              onChange={(e) => handleExamDataChange('duration', e.target.value)}
              placeholder="3h00m"
              className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
            <p className="text-xs text-gray-500 mt-1">Set exam duration (hours)</p>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">Deadline</label>
            <input
              type="text"
              value={examData.deadline}
              onChange={(e) => handleExamDataChange('deadline', e.target.value)}
              placeholder="DD/MM/YYYY AM"
              className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
            <p className="text-xs text-gray-500 mt-1">Set exam deadline</p>
          </div>
        </div>
      </div>
    </div>
  );

  // Step 2: Add Questions Choice
  const renderAddQuestions = () => (
    <div className="bg-white rounded-lg border border-gray-200 p-8">
      <h2 className="text-xl font-semibold text-gray-800 mb-6">Add Questions</h2>
      
      <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div className="border border-blue-200 rounded-lg p-6 bg-blue-50">
          <div className="text-center mb-6">
            <div className="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mx-auto mb-4">
              <Sparkles className="h-6 w-6 text-white" />
            </div>
            <h3 className="text-lg font-semibold text-gray-800 mb-2">LLM-Powered Question Creation</h3>
            <p className="text-sm text-gray-600">
              Let us generate high-quality questions based on your course materials.
            </p>
          </div>
          
          <button
            onClick={() => setCurrentStep(3)}
            className="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium"
          >
            Require AI-Generated Questions
          </button>
        </div>

        <div className="border border-green-200 rounded-lg p-6 bg-green-50">
          <div className="text-center mb-6">
            <div className="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mx-auto mb-4">
              <Edit className="h-6 w-6 text-white" />
            </div>
            <h3 className="text-lg font-semibold text-gray-800 mb-2">Manual Question Creation</h3>
            <p className="text-sm text-gray-600">
              Write your own custom questions to match the exam structure.
            </p>
          </div>
          
          <button
            onClick={() => setShowManualCreation(true)}
            className="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium"
          >
            Create Questions Manually
          </button>
        </div>
      </div>
    </div>
  );

  // Step 3: LLM-Powered Question Generation
  const renderLLMGeneration = () => (
    <div className="bg-white rounded-lg border border-gray-200 p-8">
      <h2 className="text-xl font-semibold text-gray-800 mb-6">LLM-Powered Question Generation</h2>
      
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">Select Question Type</label>
          <select
            value={llmData.questionType}
            onChange={(e) => handleLlmDataChange('questionType', e.target.value)}
            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            {questionTypes.map(type => (
              <option key={type} value={type}>{type}</option>
            ))}
          </select>
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">Number of Questions</label>
          <input
            type="number"
            value={llmData.numberOfQuestions}
            onChange={(e) => handleLlmDataChange('numberOfQuestions', parseInt(e.target.value))}
            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
          <p className="text-xs text-gray-500 mt-1">How many questions should we generate?</p>
        </div>
      </div>

      <div className="mb-6">
        <label className="block text-sm font-medium text-gray-700 mb-2">Upload Course Material</label>
        <div className="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
          <Upload className="mx-auto h-12 w-12 text-gray-400 mb-4" />
          <div className="text-sm text-gray-600 mb-2">
            <label htmlFor="file-upload" className="cursor-pointer text-blue-600 hover:text-blue-700">
              Upload a file
            </label>
            <input
              id="file-upload"
              type="file"
              className="hidden"
              onChange={handleFileUpload}
              accept=".pdf,.doc,.docx,.txt"
            />
            <span> or drag and drop</span>
          </div>
          <p className="text-xs text-gray-500">Supported formats: PDF, DOCX, TXT</p>
          {llmData.uploadedMaterial && (
            <p className="text-sm text-green-600 mt-2">
              ✓ {llmData.uploadedMaterial.name} uploaded
            </p>
          )}
        </div>
      </div>

      <button
        onClick={generateQuestionsWithLLM}
        disabled={isGenerating}
        className="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
      >
        {isGenerating ? (
          <>
            <Loader2 className="animate-spin h-5 w-5 mr-2" />
            Generating Questions...
          </>
        ) : (
          "Generate Questions"
        )}
      </button>
    </div>
  );

  // Loading state for generation
  const renderGenerating = () => (
    <div className="bg-white rounded-lg border border-gray-200 p-8 text-center">
      <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <Loader2 className="animate-spin h-8 w-8 text-green-600" />
      </div>
      <h2 className="text-xl font-semibold text-gray-800 mb-2">Generating Your Questions...</h2>
      <p className="text-gray-600">
        AI is working while we craft the perfect questions for you.
      </p>
      <div className="w-full bg-gray-200 rounded-full h-2 mt-6">
        <div className="bg-green-600 h-2 rounded-full animate-pulse" style={{width: '70%'}}></div>
      </div>
    </div>
  );

  // Step 4: Review and Organize Questions
  const renderReviewQuestions = () => (
    <div className="bg-white rounded-lg border border-gray-200 p-8">
      <h2 className="text-xl font-semibold text-gray-800 mb-6">Review and Organize Questions</h2>
      
      <div className="overflow-x-auto">
        <table className="w-full">
          <thead>
            <tr className="bg-gray-600 text-white">
              <th className="px-4 py-3 text-left text-sm font-medium">Questions</th>
              <th className="px-4 py-3 text-left text-sm font-medium">Question Type</th>
              <th className="px-4 py-3 text-left text-sm font-medium">Action</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-200">
            {generatedQuestions.map((question, index) => (
              <tr key={question.id} className="hover:bg-gray-50">
                <td className="px-4 py-4">
                  <div className="flex items-center">
                    <input
                      type="checkbox"
                      checked={question.selected}
                      onChange={() => toggleQuestionSelection(question.id)}
                      className="rounded border-gray-300 mr-3"
                    />
                    <span className="text-sm text-gray-900">{question.text}</span>
                  </div>
                </td>
                <td className="px-4 py-4">
                  <span className="text-sm text-gray-900">{question.type}</span>
                </td>
                <td className="px-4 py-4">
                  <div className="flex items-center space-x-2">
                    <button className="text-blue-600 hover:text-blue-700">
                      <Edit size={16} />
                    </button>
                    <button className="text-red-600 hover:text-red-700">
                      <Trash2 size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <div className="flex justify-center space-x-4 mt-8">
        <button
          onClick={saveDraft}
          className="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
        >
          Save Draft
        </button>
        <button
          onClick={publishExam}
          className="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        >
          Publish Exam
        </button>
      </div>
    </div>
  );

  // Manual Question Creation Modal/Page
  const renderManualCreation = () => (
    <div className="bg-white rounded-lg border border-gray-200 p-8">
      <h2 className="text-xl font-semibold text-gray-800 mb-6">Manual Essay Question Creation</h2>
      
      <div className="mb-6">
        <label className="block text-sm font-medium text-gray-700 mb-2">Number of Questions</label>
        <input
          type="number"
          value={manualQuestions.length}
          onChange={(e) => {
            const num = parseInt(e.target.value) || 1;
            const newQuestions = Array.from({length: num}, (_, i) => ({
              id: i + 1,
              text: manualQuestions[i]?.text || "",
              type: manualQuestions[i]?.type || "Short Answer"
            }));
            setManualQuestions(newQuestions);
          }}
          className="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <p className="text-xs text-gray-500 mt-1">How many questions should we create?</p>
      </div>

      <div className="space-y-6">
        {manualQuestions.map((question, index) => (
          <div key={question.id} className="border border-gray-200 rounded-lg p-4">
            <div className="flex justify-between items-center mb-4">
              <h3 className="font-medium text-gray-800">Question ({index + 1})</h3>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Enter Question Text</label>
                <textarea
                  value={question.text}
                  onChange={(e) => updateManualQuestion(question.id, 'text', e.target.value)}
                  placeholder="Define EC and e-business."
                  rows={4}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <p className="text-xs text-gray-500 mt-1">Type your essay question here</p>
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Select Question Type</label>
                <select
                  value={question.type}
                  onChange={(e) => updateManualQuestion(question.id, 'type', e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  {questionTypes.map(type => (
                    <option key={type} value={type}>{type}</option>
                  ))}
                </select>
                <p className="text-xs text-gray-500 mt-1">Choose question type</p>
              </div>
            </div>
          </div>
        ))}
      </div>

      <div className="flex justify-center space-x-4 mt-8">
        <button
          onClick={() => setShowManualCreation(false)}
          className="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
        >
          Back
        </button>
        <button
          onClick={() => {
            setShowManualCreation(false);
            setCurrentStep(4);
          }}
          className="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        >
          Continue to Review
        </button>
      </div>
    </div>
  );

  return (
    <div className="bg-gray-50 min-h-screen">
      <div className="bg-white border-b border-gray-200 px-6 py-4">
        <div className="flex items-center">
          <button 
            onClick={() => navigate('/lecturer/exam-management')}
            className="mr-4 p-2 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <ArrowLeft size={20} className="text-gray-600" />
          </button>
          <h1 className="text-2xl font-semibold text-gray-800">Create New Exam</h1>
        </div>
      </div>

      <div className="max-w-4xl mx-auto px-6 py-8">
        {showManualCreation ? (
          renderManualCreation()
        ) : (
          <>
            {currentStep === 1 && renderBasicInformation()}
            {currentStep === 2 && renderAddQuestions()}
            {currentStep === 3 && (isGenerating ? renderGenerating() : renderLLMGeneration())}
            {currentStep === 4 && renderReviewQuestions()}
          </>
        )}

        {/* Navigation Buttons */}
        {!showManualCreation && currentStep !== 3 && (
          <div className="flex justify-between mt-8">
            <button
              onClick={() => setCurrentStep(Math.max(1, currentStep - 1))}
              disabled={currentStep === 1}
              className="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Previous
            </button>
            
            {currentStep < 4 && (
              <button
                onClick={() => setCurrentStep(Math.min(4, currentStep + 1))}
                className="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
              >
                Next
              </button>
            )}
          </div>
        )}
      </div>
    </div>
  );
};

export default CreateExam;
